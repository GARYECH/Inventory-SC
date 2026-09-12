<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | FINAL TRANSACTION TYPES
    |--------------------------------------------------------------------------
    |
    | Semua bagian sistem menggunakan 4 value ini.
    |
    */

    private const TRANSACTION_TYPES = [
        'Peralatan',
        'Handy Talkie',
        'Habis Pakai',
        'Merchandise',
    ];


    /*
    |--------------------------------------------------------------------------
    | STUDENT DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ) {

        $search =
            $request->input('search');


        $type =
            $request->input('type');


        $category =
            $request->input('category');


        /*
        |--------------------------------------------------------------------------
        | OLD URL COMPATIBILITY
        |--------------------------------------------------------------------------
        |
        | Kalau ada URL lama:
        |
        | ?type=HT
        | ?type=HabisPakai
        |
        | tetap diarahkan ke nama baru.
        |
        */

        if (
            $type ===
            'HT'
        ) {

            $type =
                'Handy Talkie';

        }


        if (
            $type ===
            'HabisPakai'
        ) {

            $type =
                'Habis Pakai';

        }


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
        |
        | INI BAGIAN YANG MEMPERBAIKI BUG SCREENSHOT.
        |
        | Kalau user klik:
        |
        | Handy Talkie
        |
        | maka query menjadi:
        |
        | transaction_type = Handy Talkie
        |
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
        |
        | Category adalah filter terpisah dari Transaction Type.
        |
        */

        if ($category) {

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
        |
        | Search bisa mencari:
        |
        | - Nama barang
        | - Description
        | - Transaction Detail
        | - Subcategory
        | - Category
        |
        */

        if ($search) {

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

        /*
        |--------------------------------------------------------------------------
        | ACTIVE LOANS
        |--------------------------------------------------------------------------
        */

        $activeLoans =
            Order::where(
                'user_id',
                auth()->id()
            )
            ->whereNotIn(
                'status',
                [
                    'Returned',
                    'Returned (Damaged)',
                    'Resolved (Fine Paid)',
                    'Cancelled',
                    'Rejected',
                ]
            )
            ->with([
                'orderItems.item.category',
                'mouDocuments',
            ])
            ->latest()
            ->get();


        /*
        |--------------------------------------------------------------------------
        | PAST LOANS
        |--------------------------------------------------------------------------
        */

        $pastLoans =
            Order::where(
                'user_id',
                auth()->id()
            )
            ->whereIn(
                'status',
                [
                    'Returned',
                    'Returned (Damaged)',
                    'Resolved (Fine Paid)',
                    'Cancelled',
                    'Rejected',
                ]
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
            Item::with([
                'category',
            ])
            ->findOrFail(
                $id
            );


        $activeBookings =
            OrderItem::where(
                'item_id',
                $id
            )
            ->whereHas(
                'order',
                function (
                    $query
                ) {

                    $query->whereNotIn(
                        'status',
                        [
                            'Returned',
                            'Returned (Damaged)',
                            'Resolved (Fine Paid)',
                            'Rejected',
                            'Cancelled',
                        ]
                    );

                }
            )
            ->with([
                'order.user',
            ])
            ->get()
            ->sortBy(
                function (
                    $orderItem
                ) {

                    return optional(
                        $orderItem->order
                    )->start_date;

                }
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
    | Dipanggil oleh dashboard melalui:
    |
    | /api/check-stock/{id}
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
        | Habis Pakai dan Merchandise tidak menggunakan
        | calendar stock availability.
        |
        */

        if (
            !$item->requires_return
        ) {

            return response()->json(
                []
            );

        }


        $totalStock =
            $item->stock_quantity;


        /*
        |--------------------------------------------------------------------------
        | ACTIVE LOANS
        |--------------------------------------------------------------------------
        */

        $activeLoans =
            OrderItem::where(
                'item_id',
                $id
            )
            ->whereHas(
                'order',
                function (
                    $query
                ) {

                    $query->whereNotIn(
                        'status',
                        [
                            'Returned',
                            'Returned (Damaged)',
                            'Resolved (Fine Paid)',
                            'Rejected',
                            'Cancelled',
                        ]
                    );

                }
            )
            ->with(
                'order'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | BUILD 90-DAY STOCK CALENDAR
        |--------------------------------------------------------------------------
        */

        $availability = [];


        $startDate =
            Carbon::today()
                ->subDays(7);


        for (
            $i = 0;
            $i < 90;
            $i++
        ) {

            $date =
                $startDate
                    ->copy()
                    ->addDays($i);


            $bookedToday =
                0;


            foreach (
                $activeLoans
                as $loan
            ) {

                $order =
                    $loan->order;


                if (
                    !$order ||
                    !$order->start_date ||
                    !$order->end_date
                ) {

                    continue;

                }


                $loanStart =
                    Carbon::parse(
                        $order->start_date
                    )
                    ->toDateString();


                $loanEnd =
                    Carbon::parse(
                        $order->end_date
                    )
                    ->toDateString();


                if (
                    $date->toDateString() >=
                        $loanStart &&
                    $date->toDateString() <=
                        $loanEnd
                ) {

                    $bookedToday +=
                        $loan->quantity;

                }

            }


            $availability[
                $date->toDateString()
            ] =
                max(
                    0,
                    $totalStock -
                    $bookedToday
                );

        }


        return response()->json(
            $availability
        );
    }
}