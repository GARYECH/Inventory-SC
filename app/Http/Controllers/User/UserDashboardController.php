<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Models\Setting;
use App\Services\InventoryAvailabilityService;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    private const TRANSACTION_TYPES = [
        'Peralatan',
        'Handy Talkie',
        'Habis Pakai',
        'Merchandise',
    ];

    private const CLOSED_STATUSES = [
        'Returned',
        'Returned (Damaged)',
        'Resolved (Fine Paid)',
        'Cancelled',
        'Rejected',
    ];

    private InventoryAvailabilityService $availabilityService;

    public function __construct(
        InventoryAvailabilityService $availabilityService
    ) {
        $this->availabilityService =
            $availabilityService;
    }

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ) {
        $search = trim(
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
        | NORMALIZE TRANSACTION TYPE
        |--------------------------------------------------------------------------
        */

        $type =
            match ($type) {
                'HT' =>
                    'Handy Talkie',

                'HabisPakai' =>
                    'Habis Pakai',

                default =>
                    $type,
            };

        /*
        |--------------------------------------------------------------------------
        | ITEMS QUERY
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

        if (!empty($category)) {
            $query->whereHas(
                'category',
                function ($categoryQuery) use ($category) {
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

        if ($search !== '') {
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
                            function ($categoryQuery) use ($search) {
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
        | CATEGORIES
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
            count(
                $cart
            );

        /*
        |--------------------------------------------------------------------------
        | BAJU COLOR CHART
        |--------------------------------------------------------------------------
        |
        | Color chart hanya menjadi referensi.
        | Pemilihan warna dilakukan di Cart.
        |
        */

        $colorCharts = [];

        $colorChartSetting =
            Setting::where(
                'key',
                'baju_color_charts'
            )->value(
                'value'
            );

        if ($colorChartSetting) {
            $decodedColorCharts =
                json_decode(
                    $colorChartSetting,
                    true
                );

            if (
                is_array(
                    $decodedColorCharts
                )
            ) {
                $colorCharts =
                    $decodedColorCharts;
            }
        }

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
                'categories',
                'colorCharts'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | LOANS
    |--------------------------------------------------------------------------
    */

    public function loans()
    {
        /*
        |--------------------------------------------------------------------------
        | ACTIVE LOANS
        |--------------------------------------------------------------------------
        |
        | Pending tetap termasuk active karena belum selesai,
        | termasuk transaksi Baju.
        |
        */

        $activeLoans =
            Order::where(
                'user_id',
                auth()->id()
            )
            ->whereNotIn(
                'status',
                self::CLOSED_STATUSES
            )
            ->with([
                'orderItems.item.category',
                'orderItems.sizeBreakdowns',
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
                self::CLOSED_STATUSES
            )
            ->with([
                'orderItems.item.category',
                'orderItems.sizeBreakdowns',
                'mouDocuments',
            ])
            ->latest()
            ->paginate(5)
            ->withQueryString();

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
            )->findOrFail(
                $id
            );

        /*
        |--------------------------------------------------------------------------
        | NON-RETURNABLE
        |--------------------------------------------------------------------------
        |
        | Habis Pakai / Merchandise tidak mempunyai
        | jadwal rental.
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
        | RETURNABLE
        |--------------------------------------------------------------------------
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
        | Physical stock adalah stock yang tersedia.
        | Stock baru berkurang ketika admin approve.
        |
        */

        if (
            !$item->requires_return
        ) {
            return response()->json([
                'stock' =>
                    (int) $item->stock_quantity,

                'availability' =>
                    [],

                'bookings' =>
                    [],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | RETURNABLE
        |--------------------------------------------------------------------------
        |
        | Availability dihitung dari:
        |
        | physical stock
        | -
        | booking yang waktunya overlap
        |
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
        | FRONTEND RESPONSE
        |--------------------------------------------------------------------------
        |
        | Frontend lama membutuhkan availability map.
        |
        */

        return response()->json(
            $availability
        );
    }
}