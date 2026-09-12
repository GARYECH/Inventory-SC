<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AdminItemController extends Controller
{
    private const TRANSACTION_TYPES = [
        'Peralatan',
        'Handy Talkie',
        'Habis Pakai',
        'Merchandise',
    ];

    private const HT_DETAILS = [
        'HT UV-82',
        'HT 888s',
        'HT UV-5R',
    ];

    private const EQUIPMENT_DETAILS = [
        'Internal Rental',
        'Vendor Rental',
    ];

    private const ORDER_STATUSES = [
        'Pending',
        'Waiting for MoU',
        'Pending Review MoU',
        'Waiting for Payment',
        'Pending Review Payment',
        'Waiting for Kwitansi',
        'Pending Review Kwitansi',
        'Handed Over',
        'Pending Return Review',
        'Returned',
        'Returned (Damaged)',
        'Pending Review BA',
        'Resolved (Fine Paid)',
        'Rejected',
        'Cancelled',
    ];

    private const RESTORE_STOCK_STATUSES = [
        'Rejected',
        'Cancelled',
    ];

    private const TERMINAL_STATUSES = [
        'Returned',
        'Returned (Damaged)',
        'Resolved (Fine Paid)',
        'Rejected',
        'Cancelled',
    ];

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
            $request->input('type');

        $type = match ($type) {
            'HT' =>
                'Handy Talkie',

            'HabisPakai' =>
                'Habis Pakai',

            default =>
                $type,
        };

        $counts = [
            'total' =>
                Item::count(),

            'peralatan' =>
                Item::where(
                    'transaction_type',
                    'Peralatan'
                )->count(),

            'ht' =>
                Item::where(
                    'transaction_type',
                    'Handy Talkie'
                )->count(),

            'habispakai' =>
                Item::where(
                    'transaction_type',
                    'Habis Pakai'
                )->count(),

            'merchandise' =>
                Item::where(
                    'transaction_type',
                    'Merchandise'
                )->count(),
        ];

        $items =
            Item::with('category')
                ->when(
                    $search !== '',
                    function ($query) use ($search) {
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
                )
                ->when(
                    in_array(
                        $type,
                        self::TRANSACTION_TYPES,
                        true
                    ),
                    function ($query) use ($type) {
                        $query->where(
                            'transaction_type',
                            $type
                        );
                    }
                )
                ->latest()
                ->paginate(12)
                ->withQueryString();

        return view(
            'admin.items.index',
            compact(
                'items',
                'counts'
            )
        );
    }

    public function create()
    {
        $categories =
            Category::orderBy(
                'name'
            )->get();

        return view(
            'admin.items.create',
            compact(
                'categories'
            )
        );
    }

    public function store(
        Request $request
    ) {
        $validated =
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'category_id' => [
                    'required',
                    'exists:categories,id',
                ],

                'description' => [
                    'required',
                    'string',
                ],

                'item_photo' => [
                    'required',
                    'image',
                    'mimes:jpeg,png,jpg,webp',
                    'max:2048',
                ],

                'stock_quantity' => [
                    'required',
                    'integer',
                    'min:0',
                ],

                'price' => [
                    'required',
                    'numeric',
                    'min:0',
                ],

                'transaction_type' => [
                    'required',
                    'in:Peralatan,Handy Talkie,Habis Pakai,Merchandise',
                ],

                'transaction_detail' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'subcategory' => [
                    'nullable',
                    'string',
                    'in:Baju,ID Card,Lainnya',
                ],

                'requires_mou' => [
                    'required',
                    'boolean',
                ],
            ]);

        $this->validateTransactionStructure(
            $request
        );

        $validated['item_photo'] =
            $request
                ->file('item_photo')
                ->store(
                    'items',
                    'public'
                );

        $validated['condition_status'] =
            'Good';

        $this->normalizeTransactionFields(
            $validated
        );

        Item::create(
            $validated
        );

        return redirect()
            ->route(
                'admin.items.index'
            )
            ->with(
                'success',
                'Barang berhasil ditambahkan ke gudang!'
            );
    }

    public function edit(
        Item $item
    ) {
        $categories =
            Category::orderBy(
                'name'
            )->get();

        return view(
            'admin.items.edit',
            compact(
                'item',
                'categories'
            )
        );
    }

    public function update(
        Request $request,
        Item $item
    ) {
        $validated =
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'category_id' => [
                    'required',
                    'exists:categories,id',
                ],

                'description' => [
                    'required',
                    'string',
                ],

                'item_photo' => [
                    'nullable',
                    'image',
                    'mimes:jpeg,png,jpg,webp',
                    'max:2048',
                ],

                'stock_quantity' => [
                    'required',
                    'integer',
                    'min:0',
                ],

                'price' => [
                    'required',
                    'numeric',
                    'min:0',
                ],

                'transaction_type' => [
                    'required',
                    'in:Peralatan,Handy Talkie,Habis Pakai,Merchandise',
                ],

                'transaction_detail' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'subcategory' => [
                    'nullable',
                    'string',
                    'in:Baju,ID Card,Lainnya',
                ],

                'requires_mou' => [
                    'required',
                    'boolean',
                ],

                'condition_status' => [
                    'required',
                    'in:Good,Damaged',
                ],
            ]);

        $this->validateTransactionStructure(
            $request
        );

        $this->normalizeTransactionFields(
            $validated
        );

        /*
        |--------------------------------------------------------------------------
        | SAFETY: JANGAN MENGURANGI PHYSICAL STOCK RETURNABLE
        | DI BAWAH BARANG YANG SEDANG TERBOOKING
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $validated['transaction_type'],
                [
                    'Peralatan',
                    'Handy Talkie',
                ],
                true
            )
        ) {
            $maxBooked =
                $this->getMaximumActiveBookingQuantity(
                    $item
                );

            if (
                (int) $validated['stock_quantity']
                <
                $maxBooked
            ) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        "Stok fisik tidak dapat diatur menjadi {$validated['stock_quantity']} karena saat ini ada {$maxBooked} unit yang sedang terbooking."
                    );
            }
        }

        if (
            $request->hasFile(
                'item_photo'
            )
        ) {
            if ($item->item_photo) {
                Storage::disk('public')
                    ->delete(
                        $item->item_photo
                    );
            }

            $validated['item_photo'] =
                $request
                    ->file('item_photo')
                    ->store(
                        'items',
                        'public'
                    );
        }

        /*
        |--------------------------------------------------------------------------
        | JIKA ITEM DIUBAH DARI RETURNABLE KE NON-RETURNABLE
        |--------------------------------------------------------------------------
        |
        | Ini sengaja diblok kalau item sudah pernah dipakai transaksi,
        | supaya histori tidak rusak.
        |
        */

        if (
            $item->transaction_type !==
            $validated['transaction_type']
        ) {
            $hasHistory =
                $item->orderItems()->exists();

            if ($hasHistory) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Transaction Type barang tidak dapat diubah karena barang ini sudah memiliki histori transaksi.'
                    );
            }
        }

        $item->update(
            $validated
        );

        return redirect()
            ->route(
                'admin.items.index'
            )
            ->with(
                'success',
                'Data barang berhasil diperbarui!'
            );
    }

    private function validateTransactionStructure(
        Request $request
    ): void {
        $transactionType =
            $request->input(
                'transaction_type'
            );

        $transactionDetail =
            $request->input(
                'transaction_detail'
            );

        $subcategory =
            $request->input(
                'subcategory'
            );

        if (
            $transactionType ===
            'Handy Talkie'
        ) {
            if (
                !in_array(
                    $transactionDetail,
                    self::HT_DETAILS,
                    true
                )
            ) {
                abort(
                    422,
                    'Transaction Detail untuk Handy Talkie wajib dipilih.'
                );
            }
        }

        if (
            $transactionType ===
            'Peralatan'
        ) {
            if (
                !in_array(
                    $transactionDetail,
                    self::EQUIPMENT_DETAILS,
                    true
                )
            ) {
                abort(
                    422,
                    'Transaction Detail untuk Peralatan wajib dipilih.'
                );
            }
        }

        if (
            $transactionType ===
            'Habis Pakai'
        ) {
            if (
                $transactionDetail !== null &&
                $transactionDetail !== ''
            ) {
                abort(
                    422,
                    'Habis Pakai tidak menggunakan Transaction Detail.'
                );
            }
        }

        if (
            $transactionType ===
            'Merchandise'
        ) {
            if (
                !in_array(
                    $subcategory,
                    [
                        'Baju',
                        'ID Card',
                        'Lainnya',
                    ],
                    true
                )
            ) {
                abort(
                    422,
                    'Subcategory Merchandise wajib dipilih.'
                );
            }

            if (
                $transactionDetail !== null &&
                $transactionDetail !== ''
            ) {
                abort(
                    422,
                    'Merchandise tidak menggunakan Transaction Detail.'
                );
            }
        }

        if (
            $transactionType !==
            'Merchandise' &&
            $subcategory !== null &&
            $subcategory !== ''
        ) {
            abort(
                422,
                'Subcategory hanya boleh digunakan untuk Merchandise.'
            );
        }
    }

    private function normalizeTransactionFields(
        array &$validated
    ): void {
        $type =
            $validated['transaction_type'];

        if ($type === 'Habis Pakai') {
            $validated['transaction_detail'] =
                null;

            $validated['subcategory'] =
                null;

            $validated['requires_mou'] =
                false;
        }

        if ($type === 'Handy Talkie') {
            $validated['subcategory'] =
                null;
        }

        if ($type === 'Peralatan') {
            $validated['subcategory'] =
                null;
        }

        if ($type === 'Merchandise') {
            $validated['transaction_detail'] =
                null;

            if (
                ($validated['subcategory']
                ?? null) ===
                'Lainnya'
            ) {
                $validated['requires_mou'] =
                    false;
            }
        }
    }

    public function destroy(
        Item $item
    ) {
        if (
            $item->orderItems()->exists()
        ) {
            return back()->with(
                'error',
                'Barang tidak dapat dihapus karena sudah memiliki histori transaksi.'
            );
        }

        if ($item->item_photo) {
            Storage::disk('public')
                ->delete(
                    $item->item_photo
                );
        }

        $item->delete();

        return back()->with(
            'success',
            'Barang dihapus dari sistem.'
        );
    }

    public function orders(
        Request $request
    ) {
        $search =
            trim(
                (string) $request->input(
                    'search',
                    ''
                )
            );

        $orders =
            Order::with([
                'user',
                'orderItems.item.category',
                'mouDocuments',
            ])
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(
                        function ($q) use ($search) {
                            $q
                                ->where(
                                    'order_number',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhereHas(
                                    'user',
                                    function ($userQuery) use ($search) {
                                        $userQuery->where(
                                            'name',
                                            'like',
                                            "%{$search}%"
                                        );
                                    }
                                );
                        }
                    );
                }
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.orders.index',
            compact(
                'orders'
            )
        );
    }

    public function updateStatus(
        Request $request,
        Order $order
    ) {
        $request->validate([
            'status' =>
                'required|string',

            'mou_number' =>
                'nullable|string|max:255',

            'invoice_number' =>
                'nullable|string|max:255',

            'kwitansi_number' =>
                'nullable|string|max:255',

            'ba_number' =>
                'nullable|string|max:255',

            'ba_date' =>
                'nullable|string|max:255',

            'ba_due_date' =>
                'nullable|string|max:255',

            'ba_description' =>
                'nullable|string',

            'ba_total_fine' =>
                'nullable|numeric|min:0',
        ]);

        $newStatus =
            $request->status;

        if (
            !in_array(
                $newStatus,
                self::ORDER_STATUSES,
                true
            )
        ) {
            return back()->with(
                'error',
                'Status transaksi tidak valid.'
            );
        }

        $oldStatus =
            $order->status;

        DB::beginTransaction();

        try {
            /*
            |--------------------------------------------------------------------------
            | REJECT / CANCEL
            |--------------------------------------------------------------------------
            |
            | HANYA restore non-returnable.
            |
            */

            if (
                in_array(
                    $newStatus,
                    self::RESTORE_STOCK_STATUSES,
                    true
                ) &&
                !in_array(
                    $oldStatus,
                    self::RESTORE_STOCK_STATUSES,
                    true
                )
            ) {
                $order->load(
                    'orderItems.item'
                );

                foreach (
                    $order->orderItems
                    as $detail
                ) {
                    $item =
                        Item::lockForUpdate()
                            ->find(
                                $detail->item_id
                            );

                    if (!$item) {
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | RETURNABLE
                    |--------------------------------------------------------------------------
                    |
                    | Tidak pernah decrement saat checkout.
                    | Jadi JANGAN increment.
                    |
                    */

                    if (
                        $item->requires_return
                    ) {
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | NON-RETURNABLE
                    |--------------------------------------------------------------------------
                    */

                    $item->increment(
                        'stock_quantity',
                        $detail->quantity
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | REACTIVATE FROM REJECT / CANCEL
            |--------------------------------------------------------------------------
            |
            | HANYA decrement non-returnable.
            |
            */

            if (
                in_array(
                    $oldStatus,
                    self::RESTORE_STOCK_STATUSES,
                    true
                ) &&
                !in_array(
                    $newStatus,
                    self::RESTORE_STOCK_STATUSES,
                    true
                )
            ) {
                $order->load(
                    'orderItems.item'
                );

                foreach (
                    $order->orderItems
                    as $detail
                ) {
                    $item =
                        Item::lockForUpdate()
                            ->find(
                                $detail->item_id
                            );

                    if (!$item) {
                        continue;
                    }

                    if (
                        $item->requires_return
                    ) {
                        /*
                         * Returnable tidak menyentuh
                         * physical stock.
                         */
                        continue;
                    }

                    if (
                        (int) $item->stock_quantity
                        <
                        (int) $detail->quantity
                    ) {
                        throw new \Exception(
                            "Stok '{$item->name}' tidak cukup untuk mengaktifkan kembali transaksi."
                        );
                    }

                    $item->decrement(
                        'stock_quantity',
                        $detail->quantity
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE ORDER
            |--------------------------------------------------------------------------
            */

            $order->update([
                'status' =>
                    $newStatus,

                'mou_number' =>
                    $request->mou_number,

                'invoice_number' =>
                    $request->invoice_number,

                'kwitansi_number' =>
                    $request->kwitansi_number,

                'ba_number' =>
                    $request->ba_number,

                'ba_date' =>
                    $request->ba_date,

                'ba_due_date' =>
                    $request->ba_due_date,

                'ba_description' =>
                    $request->ba_description,

                'ba_total_fine' =>
                    $request->ba_total_fine,
            ]);

            if (
                $oldStatus !==
                $newStatus
            ) {
                $order->loadMissing(
                    'user'
                );

                if ($order->user) {
                    $order->user->notify(
                        new OrderStatusUpdated(
                            $order
                        )
                    );
                }
            }

            DB::commit();

            return back()->with(
                'success',
                'Status & dokumen berhasil diperbarui!'
            );

        } catch (
            \Throwable $e
        ) {
            DB::rollBack();

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }

    public function exportExcel()
    {
        return Excel::download(
            new OrdersExport,
            'inventory-report.xlsx'
        );
    }

    public function destroyOrder(
        $id
    ) {
        $order =
            Order::with([
                'orderItems.item',
                'mouDocuments',
            ])->findOrFail($id);

        DB::beginTransaction();

        try {
            /*
            |--------------------------------------------------------------------------
            | RESTORE STOCK
            |--------------------------------------------------------------------------
            |
            | HANYA restore non-returnable bila order masih aktif.
            |
            */

            if (
                !in_array(
                    $order->status,
                    self::TERMINAL_STATUSES,
                    true
                )
            ) {
                foreach (
                    $order->orderItems
                    as $detail
                ) {
                    $item =
                        Item::lockForUpdate()
                            ->find(
                                $detail->item_id
                            );

                    if (!$item) {
                        continue;
                    }

                    if (
                        $item->requires_return
                    ) {
                        continue;
                    }

                    $item->increment(
                        'stock_quantity',
                        $detail->quantity
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | DELETE FILES
            |--------------------------------------------------------------------------
            */

            $files = [
                $order->signed_mou,
                $order->payment_receipt,
                $order->signed_kwitansi,
                $order->signed_ba_file,
            ];

            foreach ($files as $file) {
                if (
                    !empty($file) &&
                    Storage::disk('public')
                        ->exists($file)
                ) {
                    Storage::disk('public')
                        ->delete($file);
                }
            }

            foreach (
                $order->mouDocuments
                as $document
            ) {
                if (
                    !empty(
                        $document->signed_file_path
                    ) &&
                    Storage::disk('public')
                        ->exists(
                            $document->signed_file_path
                        )
                ) {
                    Storage::disk('public')
                        ->delete(
                            $document->signed_file_path
                        );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | DELETE DOCUMENTS + ITEMS + ORDER
            |--------------------------------------------------------------------------
            */

            $order->mouDocuments()
                ->delete();

            $order->orderItems()
                ->delete();

            $order->delete();

            DB::commit();

            return back()->with(
                'success',
                'Transaksi berhasil dihapus dari sistem.'
            );

        } catch (
            \Throwable $e
        ) {
            DB::rollBack();

            return back()->with(
                'error',
                'Transaksi gagal dihapus: ' .
                $e->getMessage()
            );
        }
    }

    private function getMaximumActiveBookingQuantity(
        Item $item
    ): int {
        $bookings =
            $item->orderItems()
                ->whereHas(
                    'order',
                    function ($query) {
                        $query->whereIn(
                            'status',
                            [
                                'Pending',
                                'Waiting for MoU',
                                'Pending Review MoU',
                                'Waiting for Payment',
                                'Pending Review Payment',
                                'Waiting for Kwitansi',
                                'Pending Review Kwitansi',
                                'Handed Over',
                                'Pending Return Review',
                                'Pending Review BA',
                            ]
                        );
                    }
                )
                ->with('order')
                ->get();

        $events = [];

        foreach (
            $bookings as $booking
        ) {
            $order =
                $booking->order;

            if (
                !$order ||
                !$order->start_date ||
                !$order->end_date
            ) {
                continue;
            }

            $start =
                \Carbon\Carbon::parse(
                    $order->start_date .
                    ' ' .
                    (
                        $order->start_time
                        ?? '00:00'
                    )
                );

            $end =
                \Carbon\Carbon::parse(
                    $order->end_date .
                    ' ' .
                    (
                        $order->end_time
                        ?? '23:59'
                    )
                );

            $events[] = [
                'time' =>
                    $start->timestamp,

                'change' =>
                    (int) $booking->quantity,
            ];

            $events[] = [
                'time' =>
                    $end->timestamp,

                'change' =>
                    -(int) $booking->quantity,
            ];
        }

        usort(
            $events,
            function ($a, $b) {
                if (
                    $a['time'] ===
                    $b['time']
                ) {
                    return
                        $a['change']
                        <=>
                        $b['change'];
                }

                return
                    $a['time']
                    <=>
                    $b['time'];
            }
        );

        $current = 0;
        $maximum = 0;

        foreach ($events as $event) {
            $current +=
                $event['change'];

            $maximum =
                max(
                    $maximum,
                    $current
                );
        }

        return $maximum;
    }
}