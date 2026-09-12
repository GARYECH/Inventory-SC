<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Services\InventoryAvailabilityService;
use Carbon\Carbon;
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

    public function index(
        Request $request
    ) {
        $search = trim(
            (string) $request->input(
                'search',
                ''
            )
        );

        $type = $request->input('type');
        $category = $request->input('category');

        $type = match ($type) {
            'HT' => 'Handy Talkie',
            'HabisPakai' => 'Habis Pakai',
            default => $type,
        };

        $query = Item::query()
            ->where(
                'condition_status',
                'Good'
            )
            ->with([
                'category',
                'orderItems.order',
            ]);

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

        $items = $query
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::withCount(
            'items'
        )
            ->orderBy('name')
            ->get();

        $cart = session()->get(
            'cart',
            []
        );

        $cartCount = count($cart);

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

    public function loans()
    {
        $activeLoans = Order::where(
            'user_id',
            auth()->id()
        )
            ->whereNotIn(
                'status',
                self::CLOSED_STATUSES
            )
            ->with([
                'orderItems.item.category',
                'mouDocuments',
            ])
            ->latest()
            ->get();

        $pastLoans = Order::where(
            'user_id',
            auth()->id()
        )
            ->whereIn(
                'status',
                self::CLOSED_STATUSES
            )
            ->with([
                'orderItems.item.category',
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

    public function itemSchedule(
        $id
    ) {
        $item = Item::with(
            'category'
        )->findOrFail($id);

        if (!$item->requires_return) {
            $activeBookings = collect();

            return view(
                'user.item_schedule',
                compact(
                    'item',
                    'activeBookings'
                )
            );
        }

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

    public function checkStock(
        $id
    ) {
        $item = Item::findOrFail($id);

        if (!$item->requires_return) {
            return response()->json([
                'stock' =>
                    (int) $item->stock_quantity,

                'availability' => [],

                'bookings' => [],
            ]);
        }

        $availability =
            $this->availabilityService
                ->getDailyAvailability(
                    $item->id,
                    (int) $item->stock_quantity,
                    90
                );

        /*
        |--------------------------------------------------------------------------
        | Frontend lama hanya membutuhkan availability map.
        |--------------------------------------------------------------------------
        */

        return response()->json(
            $availability
        );
    }
}