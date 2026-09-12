<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\InventoryAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | FINAL TRANSACTION TYPES
    |--------------------------------------------------------------------------
    */

    private const TRANSACTION_TYPES = [
        'Peralatan',
        'Handy Talkie',
        'Habis Pakai',
        'Merchandise',
    ];

    private InventoryAvailabilityService $availabilityService;


    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */

    public function __construct(
        InventoryAvailabilityService $availabilityService
    ) {
        $this->availabilityService =
            $availabilityService;
    }


    /*
    |--------------------------------------------------------------------------
    | STUDENT DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ) {
        $search =
            trim(
                (string) $request->input(
                    'search',
                    ''
                )
            );

        $type =
            $request->input(
                'type'
            );

        $category =
            $request->input(
                'category'
            );


        /*
        |--------------------------------------------------------------------------
        | OLD URL COMPATIBILITY
        |--------------------------------------------------------------------------
        */

        $type = match ($type) {

            'HT' =>
                'Handy Talkie',

            'HabisPakai' =>
                'Habis Pakai',

            default =>
                $type,

        };


        /*
        |--------------------------------------------------------------------------
        | ITEM QUERY
        |--------------------------------------------------------------------------
        */

        $query =
            Item::query()
                ->where(
                    'condition_status',
                    'Good'
                )
                ->with([
                    'category',
                    'orderItems.order',
                ]);


        /*
        |--------------------------------------------------------------------------
        | TRANSACTION TYPE FILTER
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $type,
                self::TRANSACTION_TYPES,
                true
            )
        ) {

            $query->where(
                'transaction_type',
                $type
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CATEGORY FILTER
        |--------------------------------------------------------------------------
        */

        if (
            !empty($category)
        ) {

            $query->whereHas(
                'category',
                function (
                    $categoryQuery
                ) use ($category) {

                    $categoryQuery->where(
                        'slug',
                        $category
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if (
            $search !== ''
        ) {

            $query->where(
                function ($q) use ($search) {

                    $q
                        ->where(
                            'name',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'description',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'transaction_type',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'transaction_detail',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'subcategory',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhereHas(
                            'category',
                            function (
                                $categoryQuery
                            ) use ($search) {

                                $categoryQuery->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                );
                            }
                        );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $items =
            $query
                ->latest()
                ->paginate(12)
                ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | CATEGORY LIST
        |--------------------------------------------------------------------------
        */

        $categories =
            Category::withCount(
                'items'
            )
            ->orderBy(
                'name'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | CART COUNT
        |--------------------------------------------------------------------------
        */

        $cart =
            session()->get(
                'cart',
                []
            );

        $cartCount =
            count($cart);


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'user.dashboard',
            compact(
                'items',
                'type',
                'category',
                'search',
                'cartCount',
                'categories'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TRANSACTION HISTORY
    |--------------------------------------------------------------------------
    */

    public function loans()
    {
        $closedStatuses = [
            'Returned',
            'Returned (Damaged)',
            'Resolved (Fine Paid)',
            'Cancelled',
            'Rejected',
        ];


        /*
        |--------------------------------------------------------------------------
        | ACTIVE TRANSACTIONS
        |--------------------------------------------------------------------------
        */

        $activeLoans =
            Order::where(
                'user_id',
                auth()->id()
            )
            ->whereNotIn(
                'status',
                $closedStatuses
            )
            ->with([
                'orderItems.item.category',
                'mouDocuments',
            ])
            ->latest()
            ->get();


        /*
        |--------------------------------------------------------------------------
        | PAST TRANSACTIONS
        |--------------------------------------------------------------------------
        */

        $pastLoans =
            Order::where(
                'user_id',
                auth()->id()
            )
            ->whereIn(
                'status',
                $closedStatuses
            )
            ->with([
                'orderItems.item.category',
                'mouDocuments',
            ])
            ->latest()
            ->paginate(5);


        return view(
            'user.loans',
            compact(
                'activeLoans',
                'pastLoans'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ITEM SCHEDULE
    |--------------------------------------------------------------------------
    */

    public function itemSchedule(
        $id
    ) {

        $item =
            Item::with(
                'category'
            )
            ->findOrFail(
                $id
            );


        /*
        |--------------------------------------------------------------------------
        | NON-RETURNABLE ITEM
        |--------------------------------------------------------------------------
        |
        | Habis Pakai dan Merchandise tidak mempunyai
        | jadwal pengembalian.
        |
        */

        if (
            !$item->requires_return
        ) {

            $activeBookings =
                collect();

            return view(
                'user.item_schedule',
                compact(
                    'item',
                    'activeBookings'
                )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ACTIVE BOOKINGS
        |--------------------------------------------------------------------------
        |
        | Semua booking diambil dari service yang sama
        | dengan CartController.
        |
        */

        $activeBookings =
            $this->availabilityService
                ->getSchedule(
                    $item->id
                );


        return view(
            'user.item_schedule',
            compact(
                'item',
                'activeBookings'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK STOCK
    |--------------------------------------------------------------------------
    |
    | Endpoint:
    |
    | /api/check-stock/{id}
    |
    | Untuk returnable:
    |
    | tanggal => sisa stok minimum pada hari tersebut
    |
    */

    public function checkStock(
        $id
    ) {

        $item =
            Item::findOrFail(
                $id
            );


        /*
        |--------------------------------------------------------------------------
        | NON-RETURNABLE
        |--------------------------------------------------------------------------
        |
        | Untuk Habis Pakai / Merchandise,
        | stock adalah stock fisik.
        |
        */

        if (
            !$item->requires_return
        ) {

            return response()->json(
                [
                    'stock' =>
                        $item->stock_quantity,

                    'availability' =>
                        [],

                    'bookings' =>
                        [],
                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DAILY AVAILABILITY
        |--------------------------------------------------------------------------
        */

        $availability =
            $this->availabilityService
                ->getDailyAvailability(
                    $item->id,
                    (int) $item->stock_quantity,
                    90
                );


        /*
        |--------------------------------------------------------------------------
        | ACTIVE BOOKINGS
        |--------------------------------------------------------------------------
        */

        $activeBookings =
            $this->availabilityService
                ->getSchedule(
                    $item->id
                );


        /*
        |--------------------------------------------------------------------------
        | BOOKING DATA FOR FRONTEND
        |--------------------------------------------------------------------------
        */

        $bookings = [];


        foreach (
            $activeBookings as $orderItem
        ) {

            $order =
                $orderItem->order;


            if (!$order) {
                continue;
            }


            $startDate = null;

            if (
                $order->start_date
            ) {

                $startDate =
                    Carbon::parse(
                        $order->start_date
                    )->toDateString();
            }


            $endDate = null;

            if (
                $order->end_date
            ) {

                $endDate =
                    Carbon::parse(
                        $order->end_date
                    )->toDateString();
            }


            $startTime = null;

            if (
                $order->start_time
            ) {

                $startTime =
                    $this->formatTime(
                        $order->start_time
                    );
            }


            $endTime = null;

            if (
                $order->end_time
            ) {

                $endTime =
                    $this->formatTime(
                        $order->end_time
                    );
            }


            $bookings[] = [

                'quantity' =>
                    (int) $orderItem->quantity,

                'start_date' =>
                    $startDate,

                'start_time' =>
                    $startTime,

                'end_date' =>
                    $endDate,

                'end_time' =>
                    $endTime,

            ];
        }


        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        |
        | availability tetap berupa:
        |
        | {
        |     "2026-09-12": 10,
        |     "2026-09-13": 5
        | }
        |
        | Supaya frontend lama tetap compatible.
        |
        */

        return response()->json(
            $availability
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FORMAT TIME
    |--------------------------------------------------------------------------
    */

    private function formatTime(
        mixed $time
    ): ?string {

        if (
            empty($time)
        ) {
            return null;
        }


        try {

            return Carbon::parse(
                $time
            )->format(
                'H:i'
            );

        } catch (
            \Throwable $e
        ) {

            return null;
        }
    }
}