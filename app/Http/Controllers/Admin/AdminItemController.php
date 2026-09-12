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
    /*
    |--------------------------------------------------------------------------
    | FINAL TRANSACTION TYPES
    |--------------------------------------------------------------------------
    |
    | Hanya 4 Transaction Type yang boleh disimpan di database.
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
    | TRANSACTION DETAILS
    |--------------------------------------------------------------------------
    */

    private const HT_DETAILS = [
        'HT UV-82',
        'HT 888s',
        'HT UV-5R',
    ];


    private const EQUIPMENT_DETAILS = [
        'Internal Rental',
        'Vendor Rental',
    ];


    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $search =
            $request->input('search');

        $type =
            $request->input('type');


        /*
        |--------------------------------------------------------------------------
        | BACKWARD COMPATIBILITY
        |--------------------------------------------------------------------------
        |
        | Kalau masih ada URL lama seperti:
        |
        | ?type=HT
        | ?type=HabisPakai
        |
        | sistem tetap mengarah ke value baru.
        |
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
        | COUNTS
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | ITEMS
        |--------------------------------------------------------------------------
        */

        $items =
            Item::with([
                'category',
            ])
            ->when(
                $search,
                function (
                    $query,
                    $search
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
                function (
                    $query
                ) use ($type) {

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


    /*
    |--------------------------------------------------------------------------
    | CREATE
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
    | STORE
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

                /*
                 * EXACTLY 4 VALUES
                 */
                'transaction_type' => [
                    'required',
                    'in:Peralatan,Handy Talkie,Habis Pakai,Merchandise',
                ],

                /*
                 * Detail hanya divalidasi lebih lanjut
                 * setelah melihat transaction_type.
                 */
                'transaction_detail' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                /*
                 * Merchandise only.
                 */
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


        /*
        |--------------------------------------------------------------------------
        | VALIDATE TRANSACTION STRUCTURE
        |--------------------------------------------------------------------------
        */

        $this->validateTransactionStructure(
            $request
        );


        /*
        |--------------------------------------------------------------------------
        | PHOTO
        |--------------------------------------------------------------------------
        */

        $validated['item_photo'] =
            $request
                ->file('item_photo')
                ->store(
                    'items',
                    'public'
                );


        /*
        |--------------------------------------------------------------------------
        | DEFAULT CONDITION
        |--------------------------------------------------------------------------
        */

        $validated['condition_status'] =
            'Good';


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE
        |--------------------------------------------------------------------------
        */

        $this->normalizeTransactionFields(
            $validated
        );


        /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
        */

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
    | EDIT
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
    | UPDATE
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

                /*
                 * EXACTLY 4 VALUES
                 */
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
        | VALIDATE TRANSACTION STRUCTURE
        |--------------------------------------------------------------------------
        */

        $this->validateTransactionStructure(
            $request
        );


        /*
        |--------------------------------------------------------------------------
        | PHOTO
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


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE
        |--------------------------------------------------------------------------
        */

        $this->normalizeTransactionFields(
            $validated
        );


        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

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
    | TRANSACTION STRUCTURE VALIDATION
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
        |
        | Habis Pakai tidak membutuhkan transaction_detail.
        |
        */

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

        }


        /*
        |--------------------------------------------------------------------------
        | NON-MERCHANDISE CANNOT HAVE SUBCATEGORY
        |--------------------------------------------------------------------------
        */

        if (
            $transactionType !==
            'Merchandise'
        ) {

            if (
                $subcategory !== null &&
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
        | TRANSACTION DETAIL ONLY FOR SPECIFIC TYPES
        |--------------------------------------------------------------------------
        */

        if (
            $transactionType ===
            'Merchandise'
        ) {

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

    }


    /*
    |--------------------------------------------------------------------------
    | NORMALIZE TRANSACTION FIELDS
    |--------------------------------------------------------------------------
    */

    private function normalizeTransactionFields(
        array &$validated
    ): void {
        if (
    $validated['transaction_type'] ===
    'Merchandise' &&
    ($validated['subcategory'] ?? null) ===
    'Lainnya'
) {

    $validated['requires_mou'] = false;

}

        /*
        |--------------------------------------------------------------------------
        | HABIS PAKAI
        |--------------------------------------------------------------------------
        |
        | Habis Pakai tidak memerlukan MoU.
        |
        */

        if (
            $validated['transaction_type'] ===
            'Habis Pakai'
        ) {

            $validated['transaction_detail'] =
                null;

            $validated['requires_mou'] =
                false;

        }


        /*
        |--------------------------------------------------------------------------
        | HANDY TALKIE
        |--------------------------------------------------------------------------
        |
        | HT menggunakan detail model HT.
        |
        */

        if (
            $validated['transaction_type'] ===
            'Handy Talkie'
        ) {

            /*
             * Detail wajib dipertahankan.
             */

        }


        /*
        |--------------------------------------------------------------------------
        | PERALATAN
        |--------------------------------------------------------------------------
        */

        if (
            $validated['transaction_type'] !==
            'Peralatan'
        ) {

            /*
             * Bukan Peralatan =
             * tidak boleh punya Internal/Vendor Rental detail.
             */

            if (
                isset(
                    $validated['transaction_detail']
                )
            ) {

                if (
                    $validated['transaction_type'] !==
                    'Handy Talkie'
                ) {

                    $validated['transaction_detail'] =
                        null;

                }

            }

        }


        /*
        |--------------------------------------------------------------------------
        | MERCHANDISE
        |--------------------------------------------------------------------------
        */

        if (
            $validated['transaction_type'] !==
            'Merchandise'
        ) {

            $validated['subcategory'] =
                null;

        }


        /*
        |--------------------------------------------------------------------------
        | MERCHANDISE DOES NOT USE TRANSACTION DETAIL
        |--------------------------------------------------------------------------
        */

        if (
            $validated['transaction_type'] ===
            'Merchandise'
        ) {

            $validated['transaction_detail'] =
                null;

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
            $item->item_photo
        ) {

            Storage::disk('public')
                ->delete(
                    $item->item_photo
                );

        }


        $item->delete();


        return back()
            ->with(
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
            $request->input('search');


        $orders =
            Order::with([
                'user',
                'orderItems.item.category',
                'mouDocuments',
            ])
            ->when(
                $search,
                function (
                    $query,
                    $search
                ) {

                    $query
                        ->where(
                            'order_number',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhereHas(
                            'user',
                            function (
                                $q
                            ) use ($search) {

                                $q->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
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
                'nullable|numeric',

        ]);


        $oldStatus =
            $order->status;

        $newStatus =
            $request->status;


        /*
        |--------------------------------------------------------------------------
        | STATUS THAT MEANS ITEM IS NO LONGER OUT
        |--------------------------------------------------------------------------
        */

        $restockStatuses = [
            'Returned',
            'Rejected',
            'Cancelled',
            'Resolved (Fine Paid)',
        ];


        DB::beginTransaction();


        try {

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
            | RESTOCK
            |--------------------------------------------------------------------------
            |
            | Hanya Peralatan & Handy Talkie adalah returnable.
            |
            */

            if (
                in_array(
                    $newStatus,
                    $restockStatuses,
                    true
                ) &&
                !in_array(
                    $oldStatus,
                    $restockStatuses,
                    true
                )
            ) {

                foreach (
    $order->orderItems
    as $detail
) {

    $item =
        Item::find(
            $detail->item_id
        );

    if (!$item) {
        continue;
    }

    /*
    |--------------------------------------------------------------------------
    | RETURNED
    |--------------------------------------------------------------------------
    |
    | Returned hanya mengembalikan stock untuk
    | item yang memang harus dikembalikan.
    |
    */

    if (
        $newStatus === 'Returned' ||
        $newStatus === 'Returned (Damaged)' ||
        $newStatus === 'Resolved (Fine Paid)'
    ) {

        if (
            !$item->requires_return
        ) {
            continue;
        }

    }

    /*
    |--------------------------------------------------------------------------
    | REJECTED / CANCELLED
    |--------------------------------------------------------------------------
    |
    | Saat transaksi ditolak / dibatalkan,
    | stock yang sudah dikurangi saat checkout
    | harus dikembalikan.
    |
    */

    if (
        $newStatus === 'Rejected' ||
        $newStatus === 'Cancelled'
    ) {

        $item->increment(
            'stock_quantity',
            $detail->quantity
        );

        continue;
    }

    /*
    |--------------------------------------------------------------------------
    | RETURNABLE
    |--------------------------------------------------------------------------
    */

    if (
        !$item->requires_return
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
            | TAKE STOCK AGAIN
            |--------------------------------------------------------------------------
            |
            | This handles an admin moving an order back
            | from returned/cancelled into an active state.
            |
            */

            if (
                in_array(
                    $oldStatus,
                    $restockStatuses,
                    true
                ) &&
                !in_array(
                    $newStatus,
                    $restockStatuses,
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
                        !$item->requires_return
                    ) {

                        continue;

                    }


                    if (
                        $item->stock_quantity <
                        $detail->quantity
                    ) {

                        DB::rollBack();


                        return back()
                            ->with(
                                'error',
                                "Stok '{$item->name}' tidak cukup untuk mengaktifkan transaksi kembali."
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
            | NOTIFICATION
            |--------------------------------------------------------------------------
            */

            if (
                $oldStatus !==
                $newStatus
            ) {

                $order->user->notify(
                    new OrderStatusUpdated(
                        $order
                    )
                );

            }


            DB::commit();


            return back()
                ->with(
                    'success',
                    'Status & dokumen berhasil diperbarui!'
                );

        } catch (
            \Throwable $e
        ) {

            DB::rollBack();


            return back()
                ->with(
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
    $order = Order::with([
        'orderItems.item',
        'mouDocuments',
    ])->findOrFail(
        $id
    );


    /*
    |--------------------------------------------------------------------------
    | DELETE ORDER FILES
    |--------------------------------------------------------------------------
    */

    $filesToDelete = [

        /*
         * Legacy files
         */
        $order->signed_mou,

        $order->payment_receipt,

        $order->signed_kwitansi,

        $order->signed_ba_file,

    ];


    foreach (
        $filesToDelete
        as $file
    ) {

        if (
            !empty($file) &&
            Storage::disk('public')->exists(
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
    | DELETE NEW MOU FILES
    |--------------------------------------------------------------------------
    |
    | Signed MoU sekarang disimpan di:
    |
    | order_mou_documents.signed_file_path
    |
    */

    foreach (
        $order->mouDocuments
        as $mouDocument
    ) {

        if (
            !empty(
                $mouDocument->signed_file_path
            ) &&
            Storage::disk('public')->exists(
                $mouDocument->signed_file_path
            )
        ) {

            Storage::disk('public')
                ->delete(
                    $mouDocument->signed_file_path
                );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | RESTOCK ACTIVE RETURNABLE ITEMS
    |--------------------------------------------------------------------------
    |
    | Jangan tambah stok lagi kalau order sudah
    | pernah dikembalikan/dibatalkan/ditolak.
    |
    */

    $alreadyRestockedStatuses = [

        'Rejected',

        'Cancelled',

        'Returned',

        'Resolved (Fine Paid)',

    ];


    foreach (
        $order->orderItems
        as $detail
    ) {

        $item = $detail->item;


        if (!$item) {

            continue;

        }


        /*
        |--------------------------------------------------------------------------
        | ONLY RETURNABLE ITEMS
        |--------------------------------------------------------------------------
        */

        if (
            !$item->requires_return
        ) {

            continue;

        }


        /*
        |--------------------------------------------------------------------------
        | ACTIVE ORDER = RESTOCK
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $order->status,
                $alreadyRestockedStatuses,
                true
            )
        ) {

            continue;

        }


        $item->increment(
            'stock_quantity',
            $detail->quantity
        );

    }


    /*
    |--------------------------------------------------------------------------
    | DELETE MOU DOCUMENT RECORDS
    |--------------------------------------------------------------------------
    */

    $order->mouDocuments()
        ->delete();


    /*
    |--------------------------------------------------------------------------
    | DELETE ORDER ITEMS
    |--------------------------------------------------------------------------
    */

    $order->orderItems()
        ->delete();


    /*
    |--------------------------------------------------------------------------
    | DELETE ORDER
    |--------------------------------------------------------------------------
    */

    $order->delete();


    return back()
        ->with(
            'success',
            'Transaksi berhasil dihapus dari sistem.'
        );
}
}