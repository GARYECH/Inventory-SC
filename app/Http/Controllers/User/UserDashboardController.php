<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');

        $query = Item::where(
            'condition_status',
            'Good'
        )->with([
            'orderItems.order' => function ($q) {

                $q->whereNotIn(
                    'status',
                    [
                        'Returned',
                        'Resolved (Fine Paid)',
                        'Rejected',
                        'Cancelled'
                    ]
                )
                ->where(function ($query) {

                    $query
                        ->whereNull('end_date')
                        ->orWhere(
                            'end_date',
                            '>=',
                            today()
                        );

                })
                ->orderBy(
                    'start_date',
                    'asc'
                );

            }
        ]);

        if ($type) {

            if ($type === 'HT') {

                $query->whereIn(
                    'transaction_type',
                    [
                        'HT UV-82',
                        'HT 888s',
                        'HT UV-5R'
                    ]
                );

            } elseif ($type === 'HabisPakai') {

                $query->whereIn(
                    'transaction_type',
                    [
                        'ATK',
                        'Obat'
                    ]
                );

            } elseif ($type === 'Peralatan') {

                $query->where(
                    'transaction_type',
                    'Peralatan'
                );

            } elseif ($type === 'Merchandise') {

                $query->where(
                    'transaction_type',
                    'Merchandise'
                );

            }

        }

        if ($search) {

            $query->where(
                function ($q) use ($search) {

                    $q->where(
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
                        'subcategory',
                        'like',
                        "%{$search}%"
                    );

                }
            );
        }

        $items =
            $query
                ->latest()
                ->paginate(12)
                ->withQueryString();

        $cartCount =
            count(
                session()->get(
                    'cart',
                    []
                )
            );

        return view(
            'user.dashboard',
            compact(
                'items',
                'type',
                'cartCount'
            )
        );
    }

    public function loans()
    {
        $activeLoans =
            Order::where(
                'user_id',
                auth()->id()
            )
            ->whereNotIn(
                'status',
                [
                    'Returned',
                    'Resolved (Fine Paid)',
                    'Cancelled',
                    'Rejected'
                ]
            )
            ->with([
                'orderItems.item',
                'mouDocuments'
            ])
            ->latest()
            ->get();

        $pastLoans =
            Order::where(
                'user_id',
                auth()->id()
            )
            ->whereIn(
                'status',
                [
                    'Returned',
                    'Resolved (Fine Paid)',
                    'Cancelled',
                    'Rejected'
                ]
            )
            ->with([
                'orderItems.item',
                'mouDocuments'
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

    public function itemSchedule($id)
    {
        $item =
            Item::findOrFail($id);

        $activeBookings =
            OrderItem::where(
                'item_id',
                $id
            )
            ->whereHas(
                'order',
                function ($query) {

                    $query->whereNotIn(
                        'status',
                        [
                            'Returned',
                            'Resolved (Fine Paid)',
                            'Rejected',
                            'Cancelled'
                        ]
                    );

                }
            )
            ->with([
                'order.user'
            ])
            ->get()
            ->sortBy(
                function ($orderItem) {

                    return
                        $orderItem
                            ->order
                            ->start_date;

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

    public function checkStock($id)
    {
        $item =
            Item::findOrFail($id);

        $totalStock =
            $item->stock_quantity;

        $activeLoans =
            OrderItem::where(
                'item_id',
                $id
            )
            ->whereHas(
                'order',
                function ($query) {

                    $query->whereNotIn(
                        'status',
                        [
                            'Returned',
                            'Resolved (Fine Paid)',
                            'Rejected',
                            'Cancelled'
                        ]
                    );

                }
            )
            ->with('order')
            ->get();

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

            $bookedToday = 0;

            foreach (
                $activeLoans as $loan
            ) {

                if (
                    !$loan->order ||
                    !$loan->order->start_date ||
                    !$loan->order->end_date
                ) {
                    continue;
                }

                $loanStart =
                    Carbon::parse(
                        $loan->order->start_date
                    )->toDateString();

                $loanEnd =
                    Carbon::parse(
                        $loan->order->end_date
                    )->toDateString();

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