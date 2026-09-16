<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Notifications\OrderStatusUpdated;
use App\Services\InventoryAvailabilityService;
use Carbon\Carbon;
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
        'Approved',
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

    /*
    |--------------------------------------------------------------------------
    | STATUS YANG BERARTI STOCK NON-RETURNABLE SUDAH DIAMBIL
    |--------------------------------------------------------------------------
    |
    | Pending TIDAK termasuk.
    |
    | Physical stock baru berkurang ketika transaksi masuk ke status
    | Approved, kemudian dianggap sudah terpakai selama proses transaksi
    | berjalan.
    |
    */

    private const STOCK_CONSUMED_STATUSES = [
        'Approved',
        'Waiting for MoU',
        'Pending Review MoU',
        'Waiting for Payment',
        'Pending Review Payment',
        'Waiting for Kwitansi',
        'Pending Review Kwitansi',
        'Handed Over',
        'Pending Return Review',
        'Pending Review BA',
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
    | ITEM INDEX
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

        $type =
            match ($type) {
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
            Item::with(
                'category'
            )
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
            ->when(
                !empty($category),
                function ($query) use ($category) {
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
            )
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories =
            Category::withCount(
                'items'
            )
            ->orderBy(
                'name'
            )
            ->get();

        return view(
            'admin.items.index',
            compact(
                'items',
                'counts',
                'categories',
                'category'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE ITEM
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | STORE ITEM
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | EDIT ITEM
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | UPDATE ITEM
    |--------------------------------------------------------------------------
    */

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

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION STRUCTURE
        |--------------------------------------------------------------------------
        */

        $this->validateTransactionStructure(
            $request
        );

        $this->normalizeTransactionFields(
            $validated
        );

        /*
        |--------------------------------------------------------------------------
        | DO NOT LOWER RETURNABLE STOCK BELOW ACTIVE BOOKINGS
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

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION TYPE CANNOT CHANGE AFTER HISTORY
        |--------------------------------------------------------------------------
        */

        if (
            $item->transaction_type
            !==
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

        /*
        |--------------------------------------------------------------------------
        | ITEM PHOTO
        |--------------------------------------------------------------------------
        */

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

    /*
    |--------------------------------------------------------------------------
    | VALIDATE TRANSACTION STRUCTURE
    |--------------------------------------------------------------------------
    */

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

        /*
        |--------------------------------------------------------------------------
        | HANDY TALKIE
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | PERALATAN
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | HABIS PAKAI
        |--------------------------------------------------------------------------
        */

        if (
            $transactionType ===
            'Habis Pakai'
        ) {
            if (
                $transactionDetail !== null
                &&
                $transactionDetail !== ''
            ) {
                abort(
                    422,
                    'Habis Pakai tidak menggunakan Transaction Detail.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | MERCHANDISE
        |--------------------------------------------------------------------------
        */

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
                $transactionDetail !== null
                &&
                $transactionDetail !== ''
            ) {
                abort(
                    422,
                    'Merchandise tidak menggunakan Transaction Detail.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SUBCATEGORY ONLY FOR MERCHANDISE
        |--------------------------------------------------------------------------
        */

        if (
            $transactionType !==
            'Merchandise'
            &&
            $subcategory !== null
            &&
            $subcategory !== ''
        ) {
            abort(
                422,
                'Subcategory hanya boleh digunakan untuk Merchandise.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | NORMALIZE TRANSACTION FIELDS
    |--------------------------------------------------------------------------
    */

    private function normalizeTransactionFields(
        array &$validated
    ): void {
        $type =
            $validated['transaction_type'];

        /*
        | Habis Pakai
        */

        if (
            $type ===
            'Habis Pakai'
        ) {
            $validated['transaction_detail'] =
                null;

            $validated['subcategory'] =
                null;

            $validated['requires_mou'] =
                false;
        }

        /*
        | Handy Talkie
        */

        if (
            $type ===
            'Handy Talkie'
        ) {
            $validated['subcategory'] =
                null;
        }

        /*
        | Peralatan
        */

        if (
            $type ===
            'Peralatan'
        ) {
            $validated['subcategory'] =
                null;
        }

        /*
        | Merchandise
        */

        if (
            $type ===
            'Merchandise'
        ) {
            $validated['transaction_detail'] =
                null;

            if (
                ($validated['subcategory'] ?? null)
                ===
                'Lainnya'
            ) {
                $validated['requires_mou'] =
                    false;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE ITEM
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | ORDERS
    |--------------------------------------------------------------------------
    */

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
                'orderItems.sizeBreakdowns',
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
                                    function (
                                        $userQuery
                                    ) use ($search) {
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

    /*
    |--------------------------------------------------------------------------
    | UPDATE ORDER STATUS
    |--------------------------------------------------------------------------
    */

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

        /*
        |--------------------------------------------------------------------------
        | PENDING CANNOT SKIP APPROVAL
        |--------------------------------------------------------------------------
        |
        | Dari Pending, transaksi hanya boleh:
        |
        | Pending -> Approved
        | Pending -> Rejected
        | Pending -> Cancelled
        |
        | Tidak boleh langsung lompat ke Waiting for MoU,
        | Waiting for Payment, dan seterusnya.
        |
        */

        if (
            $oldStatus ===
            'Pending'
            &&
            !in_array(
                $newStatus,
                [
                    'Approved',
                    'Rejected',
                    'Cancelled',
                ],
                true
            )
        ) {
            return back()->with(
                'error',
                'Transaksi Pending harus disetujui terlebih dahulu sebelum diproses ke tahap berikutnya.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | PENDING CANNOT GO BACK
        |--------------------------------------------------------------------------
        */

        if (
            $oldStatus !==
            'Pending'
            &&
            $newStatus ===
            'Pending'
        ) {
            return back()->with(
                'error',
                'Transaksi yang sudah diproses tidak dapat dikembalikan ke status Pending.'
            );
        }

        DB::beginTransaction();

        try {
            $order->load([
                'orderItems.item',
            ]);

            /*
            |--------------------------------------------------------------------------
            | PENDING -> APPROVED
            |--------------------------------------------------------------------------
            |
            | Returnable:
            | - cek availability
            | - physical stock tetap
            |
            | Non-returnable:
            | - cek physical stock
            | - decrement physical stock
            |
            */

            if (
                $oldStatus ===
                'Pending'
                &&
                $newStatus ===
                'Approved'
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
                        throw new \Exception(
                            'Barang pada transaksi tidak ditemukan.'
                        );
                    }

                    $requestedQty =
                        (int) $detail->quantity;

                    /*
                    |--------------------------------------------------------------------------
                    | RETURNABLE
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $item->requires_return
                    ) {
                        if (
                            !$order->start_date ||
                            !$order->end_date ||
                            !$order->start_time ||
                            !$order->end_time
                        ) {
                            throw new \Exception(
                                "Jadwal transaksi untuk '{$item->name}' belum lengkap."
                            );
                        }

                        $startDate =
                            $order->start_date
                                ->format(
                                    'Y-m-d'
                                );

                        $startTime =
                            $order->start_time
                                ->format(
                                    'H:i:s'
                                );

                        $endDate =
                            $order->end_date
                                ->format(
                                    'Y-m-d'
                                );

                        $endTime =
                            $order->end_time
                                ->format(
                                    'H:i:s'
                                );

                        $overlappingQty =
                            $this->availabilityService
                                ->getOverlappingQuantity(
                                    $item->id,
                                    $startDate,
                                    $startTime,
                                    $endDate,
                                    $endTime,
                                    $order->id
                                );

                        if (
                            $overlappingQty
                            +
                            $requestedQty
                            >
                            (int) $item->stock_quantity
                        ) {
                            $remaining =
                                max(
                                    0,
                                    (int) $item->stock_quantity
                                    -
                                    $overlappingQty
                                );

                            throw new \Exception(
                                "Tidak dapat Approve transaksi. Stok '{$item->name}' pada jadwal tersebut hanya tersisa {$remaining} unit."
                            );
                        }

                        /*
                        | Physical stock tidak berubah.
                        */
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | NON-RETURNABLE
                    |--------------------------------------------------------------------------
                    |
                    | Stock baru berkurang sekarang.
                    |
                    */

                    if (
                        $requestedQty
                        >
                        (int) $item->stock_quantity
                    ) {
                        throw new \Exception(
                            "Stok '{$item->name}' tidak cukup untuk menyetujui transaksi. Stok tersedia: {$item->stock_quantity} unit."
                        );
                    }

                    $item->decrement(
                        'stock_quantity',
                        $requestedQty
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | REJECT / CANCEL
            |--------------------------------------------------------------------------
            |
            | Hanya restore kalau sebelumnya stock memang sudah dikonsumsi.
            |
            | Pending -> Rejected
            | Pending -> Cancelled
            |
            | tidak restore.
            |
            | Approved -> Rejected
            | Approved -> Cancelled
            |
            | restore non-returnable.
            |
            */

            if (
                in_array(
                    $newStatus,
                    self::RESTORE_STOCK_STATUSES,
                    true
                )
                &&
                $this->statusConsumesPhysicalStock(
                    $oldStatus
                )
            ) {
                $this->restoreNonReturnableStock(
                    $order
                );
            }

            /*
            |--------------------------------------------------------------------------
            | REACTIVATE FROM REJECT / CANCEL
            |--------------------------------------------------------------------------
            |
            | Untuk menjaga stock tetap konsisten kalau admin membuka
            | kembali transaksi yang sebelumnya ditolak/dibatalkan.
            |
            */

            if (
                in_array(
                    $oldStatus,
                    self::RESTORE_STOCK_STATUSES,
                    true
                )
                &&
                $newStatus ===
                'Approved'
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
                        throw new \Exception(
                            'Barang pada transaksi tidak ditemukan.'
                        );
                    }

                    $requestedQty =
                        (int) $detail->quantity;

                    /*
                    | Returnable
                    */

                    if (
                        $item->requires_return
                    ) {
                        if (
                            !$order->start_date ||
                            !$order->end_date ||
                            !$order->start_time ||
                            !$order->end_time
                        ) {
                            throw new \Exception(
                                "Jadwal transaksi untuk '{$item->name}' belum lengkap."
                            );
                        }

                        $overlappingQty =
                            $this->availabilityService
                                ->getOverlappingQuantity(
                                    $item->id,
                                    $order->start_date->format(
                                        'Y-m-d'
                                    ),
                                    $order->start_time->format(
                                        'H:i:s'
                                    ),
                                    $order->end_date->format(
                                        'Y-m-d'
                                    ),
                                    $order->end_time->format(
                                        'H:i:s'
                                    ),
                                    $order->id
                                );

                        if (
                            $overlappingQty
                            +
                            $requestedQty
                            >
                            (int) $item->stock_quantity
                        ) {
                            $remaining =
                                max(
                                    0,
                                    (int) $item->stock_quantity
                                    -
                                    $overlappingQty
                                );

                            throw new \Exception(
                                "Tidak dapat mengaktifkan kembali transaksi. Stok '{$item->name}' pada jadwal tersebut hanya tersisa {$remaining} unit."
                            );
                        }

                        continue;
                    }

                    /*
                    | Non-returnable
                    */

                    if (
                        (int) $item->stock_quantity
                        <
                        $requestedQty
                    ) {
                        throw new \Exception(
                            "Stok '{$item->name}' tidak cukup untuk mengaktifkan kembali transaksi."
                        );
                    }

                    $item->decrement(
                        'stock_quantity',
                        $requestedQty
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

            /*
            |--------------------------------------------------------------------------
            | USER NOTIFICATION
            |--------------------------------------------------------------------------
            */

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

    /*
    |--------------------------------------------------------------------------
    | EXPORT EXCEL
    |--------------------------------------------------------------------------
    */

    public function exportExcel()
    {
        return Excel::download(
            new OrdersExport,
            'inventory-report.xlsx'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE ORDER
    |--------------------------------------------------------------------------
    */

    public function destroyOrder(
        $id
    ) {
        $order =
            Order::with([
                'orderItems.item',
                'mouDocuments',
            ])
            ->findOrFail(
                $id
            );

        DB::beginTransaction();

        try {
            /*
            |--------------------------------------------------------------------------
            | RESTORE STOCK ONLY IF IT WAS CONSUMED
            |--------------------------------------------------------------------------
            |
            | Pending:
            | tidak restore.
            |
            | Approved / process:
            | restore non-returnable.
            |
            | Returnable:
            | tidak restore physical stock.
            |
            */

            if (
                $this->statusConsumesPhysicalStock(
                    $order->status
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
                        (int) $detail->quantity
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

            foreach (
                $files
                as $file
            ) {
                if (
                    !empty($file)
                    &&
                    Storage::disk('public')
                        ->exists(
                            $file
                        )
                ) {
                    Storage::disk('public')
                        ->delete(
                            $file
                        );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | DELETE SIGNED MOU FILES
            |--------------------------------------------------------------------------
            */

            foreach (
                $order->mouDocuments
                as $document
            ) {
                if (
                    !empty(
                        $document->signed_file_path
                    )
                    &&
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
            | DELETE RELATED DATA
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

    /*
    |--------------------------------------------------------------------------
    | CHECK MAXIMUM ACTIVE BOOKING
    |--------------------------------------------------------------------------
    */

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
                                'Approved',
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
                ->with(
                    'order'
                )
                ->get();

        $events = [];

        foreach (
            $bookings
            as $booking
        ) {
            $order =
                $booking->order;

            if (
                !$order
                ||
                !$order->start_date
                ||
                !$order->end_date
            ) {
                continue;
            }

            $startDate =
                $order->start_date
                    ->format(
                        'Y-m-d'
                    );

            $startTime =
                $order->start_time
                    ? $order->start_time
                        ->format(
                            'H:i:s'
                        )
                    : '00:00:00';

            $endDate =
                $order->end_date
                    ->format(
                        'Y-m-d'
                    );

            $endTime =
                $order->end_time
                    ? $order->end_time
                        ->format(
                            'H:i:s'
                        )
                    : '23:59:59';

            $start =
                Carbon::parse(
                    "{$startDate} {$startTime}"
                );

            $end =
                Carbon::parse(
                    "{$endDate} {$endTime}"
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
            function (
                $a,
                $b
            ) {
                if (
                    $a['time']
                    ===
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

        foreach (
            $events
            as $event
        ) {
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

    /*
    |--------------------------------------------------------------------------
    | CHECK WHETHER STATUS HAS CONSUMED NON-RETURNABLE STOCK
    |--------------------------------------------------------------------------
    */

    private function statusConsumesPhysicalStock(
        ?string $status
    ): bool {
        return in_array(
            $status,
            self::STOCK_CONSUMED_STATUSES,
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RESTORE NON-RETURNABLE STOCK
    |--------------------------------------------------------------------------
    */

    private function restoreNonReturnableStock(
        Order $order
    ): void {
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
            | Returnable tidak pernah mengurangi physical stock.
            */

            if (
                $item->requires_return
            ) {
                continue;
            }

            $item->increment(
                'stock_quantity',
                (int) $detail->quantity
            );
        }
    }
}