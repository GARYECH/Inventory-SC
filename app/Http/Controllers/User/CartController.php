<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderMouDocument;
use App\Models\User;
use App\Notifications\AdminNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    private const TRANSACTION_TYPES = [
        'Peralatan',
        'Handy Talkie',
        'Habis Pakai',
        'Merchandise',
    ];

    private const TIME_OPTIONS = [
        '17:00',
        '17:30',
        '18:00',
        '18:30',
        '19:00',
    ];

    public function viewCart()
    {
        $cart = session()->get('cart', []);

        return view(
            'user.cart',
            compact('cart')
        );
    }


    public function addToCart(
        Request $request,
        Item $item
    ) {
        $cart = session()->get('cart', []);

        $requestQuantity = (int) (
            $request->quantity ?? 1
        );


        /*
        |--------------------------------------------------------------------------
        | VALIDATE TRANSACTION TYPE
        |--------------------------------------------------------------------------
        */

        if (!in_array(
            $item->transaction_type,
            self::TRANSACTION_TYPES,
            true
        )) {
            return back()->with(
                'error',
                'Transaction Type barang tidak valid.'
            );
        }


        $transactionType =
            $item->transaction_type;


        /*
        |--------------------------------------------------------------------------
        | SAME TRANSACTION TYPE
        |--------------------------------------------------------------------------
        */

        if (!empty($cart)) {

            $firstItem = reset($cart);

            $firstTransactionType =
                $firstItem['transaction_type'] ?? null;


            if (
                $firstTransactionType !==
                $transactionType
            ) {

                return back()->with(
                    'error',
                    'Satu transaksi hanya dapat berisi barang dengan Transaction Type yang sama.'
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | MERCHANDISE
        |--------------------------------------------------------------------------
        */

        $size = null;

        $designLink = null;

        $sizeAdditionalPrice = 0;


        if (
            $transactionType ===
            'Merchandise'
        ) {

            $subcategory =
                $item->subcategory;


            if (!in_array(
                $subcategory,
                [
                    'Baju',
                    'ID Card',
                    'Lainnya',
                ],
                true
            )) {

                return back()->with(
                    'error',
                    'Subcategory Merchandise belum ditentukan.'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | BAJU
            |--------------------------------------------------------------------------
            */

            if (
                $subcategory ===
                'Baju'
            ) {

                $request->validate([
                    'size' => [
                        'required',
                        'in:S,M,L,XL,2XL,3XL,4XL,5XL',
                    ],

                    'design_link' => [
                        'required',
                        'url',
                        'max:2000',
                    ],
                ]);


                $size =
                    $request->size;


                $designLink =
                    $request->design_link;


                $sizeAdditionalPrice =
                    $this->getSizeAdditionalPrice(
                        $size
                    );

            }


            /*
            |--------------------------------------------------------------------------
            | ID CARD
            |--------------------------------------------------------------------------
            */

            if (
                $subcategory ===
                'ID Card'
            ) {

                $request->validate([
                    'design_link' => [
                        'required',
                        'url',
                        'max:2000',
                    ],
                ]);


                $designLink =
                    $request->design_link;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | COMMON VALIDATION
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'start_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'start_time' => [
                'required',
                'date_format:H:i',

                function (
                    $attribute,
                    $value,
                    $fail
                ) {

                    if (!$this->isValidTime($value)) {

                        $fail(
                            'Jam transaksi hanya boleh antara 17:00 sampai 19:00.'
                        );

                    }

                },
            ],
        ]);


        $startDate =
            $request->start_date;

        $startTime =
            $request->start_time;


        $endDate = null;

        $endTime = null;


        /*
        |--------------------------------------------------------------------------
        | RETURN SCHEDULE
        |--------------------------------------------------------------------------
        */

        $isRental =
            $this->requiresReturn(
                $transactionType
            );


        if ($isRental) {

            $request->validate([
                'end_date' => [
                    'required',
                    'date',
                    'after_or_equal:start_date',
                ],

                'end_time' => [
                    'required',
                    'date_format:H:i',

                    function (
                        $attribute,
                        $value,
                        $fail
                    ) {

                        if (!$this->isValidTime($value)) {

                            $fail(
                                'Jam pengembalian hanya boleh antara 17:00 sampai 19:00.'
                            );

                        }

                    },
                ],
            ]);


            $endDate =
                $request->end_date;


            $endTime =
                $request->end_time;


            $startDateTime =
                Carbon::parse(
                    "{$startDate} {$startTime}"
                );


            $endDateTime =
                Carbon::parse(
                    "{$endDate} {$endTime}"
                );


            if (
                $endDateTime->lessThanOrEqualTo(
                    $startDateTime
                )
            ) {

                return back()->with(
                    'error',
                    'Waktu pengembalian harus setelah waktu pengambilan.'
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | SAME SCHEDULE
        |--------------------------------------------------------------------------
        */

        if (!empty($cart)) {

            $existing =
                reset($cart);


            if (
                ($existing['start_date'] ?? null) !==
                    $startDate ||
                ($existing['start_time'] ?? null) !==
                    $startTime
            ) {

                return back()->with(
                    'error',
                    'Semua barang dalam satu transaksi harus menggunakan tanggal dan jam transaksi yang sama.'
                );

            }


            $existingRequiresReturn =
                $this->requiresReturn(
                    $existing['transaction_type'] ?? ''
                );


            if (
                $isRental &&
                $existingRequiresReturn
            ) {

                if (
                    ($existing['end_date'] ?? null) !==
                        $endDate ||
                    ($existing['end_time'] ?? null) !==
                        $endTime
                ) {

                    return back()->with(
                        'error',
                        'Semua barang rental dalam satu transaksi harus menggunakan jadwal pengembalian yang sama.'
                    );

                }

            }

        }


        /*
        |--------------------------------------------------------------------------
        | LINE KEY
        |--------------------------------------------------------------------------
        */

        $lineKey =
            $this->buildLineKey(
                $item,
                $size
            );


        $existingQuantity =
            (int) (
                $cart[$lineKey]['quantity']
                ?? 0
            );


        /*
        |--------------------------------------------------------------------------
        | STOCK
        |--------------------------------------------------------------------------
        */

        if ($isRental) {

            $overlappingQty =
                $this->getOverlappingQuantity(
                    $item->id,
                    $startDate,
                    $startTime,
                    $endDate,
                    $endTime
                );


            $totalRequested =
                $overlappingQty +
                $existingQuantity +
                $requestQuantity;


            if (
                $totalRequested >
                $item->stock_quantity
            ) {

                $remaining = max(
                    0,
                    $item->stock_quantity -
                    $overlappingQty -
                    $existingQuantity
                );


                return back()->with(
                    'error',
                    "Gagal! Sisa stok '{$item->name}' untuk jadwal tersebut hanya {$remaining} unit."
                );

            }

        } else {

            if (
                $existingQuantity +
                $requestQuantity >
                $item->stock_quantity
            ) {

                return back()->with(
                    'error',
                    "Gagal! Stok '{$item->name}' hanya {$item->stock_quantity} unit."
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | SAVE CART
        |--------------------------------------------------------------------------
        */

        $cart[$lineKey] = [

            'id' =>
                $item->id,

            'name' =>
                $item->name,

            'price' =>
                $item->price,

            'quantity' =>
                $existingQuantity +
                $requestQuantity,

            'transaction_type' =>
                $item->transaction_type,

            'transaction_detail' =>
                $item->transaction_detail,

            'category_id' =>
                $item->category_id,

            'category_name' =>
                optional(
                    $item->category
                )->name,

            'subcategory' =>
                $item->subcategory,

            'requires_mou' =>
                $item->requires_mou,

            'start_date' =>
                $startDate,

            'start_time' =>
                $startTime,

            'end_date' =>
                $endDate,

            'end_time' =>
                $endTime,

            'size' =>
                $size,

            'design_link' =>
                $designLink,

            'size_additional_price' =>
                $sizeAdditionalPrice,
        ];


        session()->put(
            'cart',
            $cart
        );


        return back()->with(
            'success',
            'Barang berhasil masuk keranjang!'
        );
    }


    public function clearCart()
    {
        session()->forget('cart');

        return back()->with(
            'success',
            'Keranjang dikosongkan.'
        );
    }


    public function updateCart(
        Request $request,
        $lineKey
    ) {
        $cart =
            session()->get(
                'cart',
                []
            );


        if (!isset($cart[$lineKey])) {

            return back()->with(
                'error',
                'Barang tidak ditemukan di keranjang.'
            );

        }


        $request->validate([
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);


        $item =
            Item::findOrFail(
                $cart[$lineKey]['id']
            );


        $quantity =
            (int) $request->quantity;


        $transactionType =
            $item->transaction_type;


        if (!in_array(
            $transactionType,
            self::TRANSACTION_TYPES,
            true
        )) {

            return back()->with(
                'error',
                'Transaction Type barang tidak valid.'
            );

        }


        $isRental =
            $this->requiresReturn(
                $transactionType
            );


        if ($isRental) {

            $overlappingQty =
                $this->getOverlappingQuantity(
                    $item->id,
                    $cart[$lineKey]['start_date'],
                    $cart[$lineKey]['start_time'],
                    $cart[$lineKey]['end_date'],
                    $cart[$lineKey]['end_time']
                );


            if (
                $overlappingQty +
                $quantity >
                $item->stock_quantity
            ) {

                return back()->with(
                    'error',
                    "Stok '{$item->name}' tidak mencukupi untuk jadwal tersebut."
                );

            }

        } else {

            if (
                $quantity >
                $item->stock_quantity
            ) {

                return back()->with(
                    'error',
                    "Stok '{$item->name}' tidak mencukupi."
                );

            }

        }


        $cart[$lineKey]['quantity'] =
            $quantity;


        session()->put(
            'cart',
            $cart
        );


        return back()->with(
            'success',
            'Jumlah barang berhasil diupdate.'
        );
    }


    public function removeItem(
        $lineKey
    ) {
        $cart =
            session()->get(
                'cart',
                []
            );


        if (isset($cart[$lineKey])) {

            unset(
                $cart[$lineKey]
            );


            session()->put(
                'cart',
                $cart
            );

        }


        return back()->with(
            'success',
            'Barang berhasil dihapus dari keranjang.'
        );
    }


    public function processCheckout(
        Request $request
    ) {
        $cart =
            session()->get(
                'cart',
                []
            );


        if (empty($cart)) {

            return back()->with(
                'error',
                'Keranjangmu kosong!'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | FORM VALIDATION
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'full_name' =>
                'required|string|max:255',

            'organization' =>
                'required|string|max:255',

            'position' =>
                'required|string|max:255',

            'phone_number' =>
                'required|string|max:50',

            'proker_name' =>
                'required|string|max:255',

            'ketua_acara' =>
                'required|string|max:255',

            'treasurer_name' =>
                'required|string|max:255',

            'address' =>
                'required|string',

            'notes' =>
                'nullable|string',

            'is_sop_accepted' =>
                'required|accepted',

        ]);


        /*
        |--------------------------------------------------------------------------
        | TRANSACTION TYPE CHECK
        |--------------------------------------------------------------------------
        */

        $firstItem =
            reset($cart);


        $orderType =
            $firstItem[
                'transaction_type'
            ]
            ?? null;


        if (!in_array(
            $orderType,
            self::TRANSACTION_TYPES,
            true
        )) {

            return back()->with(
                'error',
                'Transaction Type tidak valid.'
            );

        }


        foreach ($cart as $cartItem) {

            if (
                ($cartItem['transaction_type'] ?? null)
                !==
                $orderType
            ) {

                return back()->with(
                    'error',
                    'Semua barang dalam satu transaksi harus memiliki Transaction Type yang sama.'
                );

            }


            if (
                ($cartItem['start_date'] ?? null) !==
                    ($firstItem['start_date'] ?? null) ||
                ($cartItem['start_time'] ?? null) !==
                    ($firstItem['start_time'] ?? null)
            ) {

                return back()->with(
                    'error',
                    'Semua barang dalam satu transaksi harus menggunakan tanggal dan jam transaksi yang sama.'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | RETURN SCHEDULE
            |--------------------------------------------------------------------------
            */

            $cartItemIsRental =
                $this->requiresReturn(
                    $cartItem[
                        'transaction_type'
                    ]
                );


            if ($cartItemIsRental) {

                if (
                    ($cartItem['end_date'] ?? null) !==
                        ($firstItem['end_date'] ?? null) ||
                    ($cartItem['end_time'] ?? null) !==
                        ($firstItem['end_time'] ?? null)
                ) {

                    return back()->with(
                        'error',
                        'Semua barang rental dalam satu transaksi harus menggunakan jadwal pengembalian yang sama.'
                    );

                }

            }

        }


        /*
        |--------------------------------------------------------------------------
        | MERCHANDISE REVALIDATION
        |--------------------------------------------------------------------------
        */

        foreach ($cart as $cartItem) {

            if (
                $orderType !==
                'Merchandise'
            ) {
                break;
            }


            $subcategory =
                $cartItem[
                    'subcategory'
                ]
                ?? null;


            if (
                $subcategory ===
                'Baju'
            ) {

                if (
                    empty(
                        $cartItem['size']
                    ) ||
                    empty(
                        $cartItem['design_link']
                    )
                ) {

                    return back()->with(
                        'error',
                        'Merchandise Baju membutuhkan ukuran dan link desain.'
                    );

                }

            }


            if (
                $subcategory ===
                'ID Card'
            ) {

                if (
                    empty(
                        $cartItem['design_link']
                    )
                ) {

                    return back()->with(
                        'error',
                        'Merchandise ID Card membutuhkan link desain.'
                    );

                }

            }

        }


        /*
        |--------------------------------------------------------------------------
        | TOTAL PRICE
        |--------------------------------------------------------------------------
        */

        $totalPrice = 0;


        foreach ($cart as $cartItem) {

            $unitPrice =
                (int) (
                    $cartItem['price']
                    ?? 0
                );


            $unitPrice +=
                (int) (
                    $cartItem[
                        'size_additional_price'
                    ]
                    ?? 0
                );


            /*
            |--------------------------------------------------------------------------
            | STUDENT COUNCIL FREE FACILITY
            |--------------------------------------------------------------------------
            */

            if (
                $request->organization ===
                'Student Council' &&
                $orderType ===
                'Habis Pakai'
            ) {

                $unitPrice = 0;

            }


            $totalPrice +=
                $unitPrice *
                (int) (
                    $cartItem['quantity']
                );

        }


        /*
        |--------------------------------------------------------------------------
        | DATABASE TRANSACTION
        |--------------------------------------------------------------------------
        */

        DB::beginTransaction();


        try {

            /*
            |--------------------------------------------------------------------------
            | FINAL STOCK CHECK
            |--------------------------------------------------------------------------
            */

            foreach ($cart as $cartItem) {

                $dbItem =
                    Item::lockForUpdate()
                        ->find(
                            $cartItem['id']
                        );


                if (!$dbItem) {

                    throw new \Exception(
                        "Barang {$cartItem['name']} tidak ditemukan."
                    );

                }


                if (
                    $dbItem->transaction_type !==
                    $cartItem['transaction_type']
                ) {

                    throw new \Exception(
                        "Transaction Type barang '{$dbItem->name}' sudah berubah."
                    );

                }


                $requiresReturn =
                    $this->requiresReturn(
                        $cartItem[
                            'transaction_type'
                        ]
                    );


                if ($requiresReturn) {

                    $overlappingQty =
                        $this->getOverlappingQuantity(
                            $dbItem->id,
                            $cartItem['start_date'],
                            $cartItem['start_time'],
                            $cartItem['end_date'],
                            $cartItem['end_time']
                        );


                    if (
                        $overlappingQty +
                        $cartItem['quantity'] >
                        $dbItem->stock_quantity
                    ) {

                        throw new \Exception(
                            "Stok '{$dbItem->name}' tidak mencukupi untuk jadwal tersebut."
                        );

                    }

                } else {

                    if (
                        $dbItem->stock_quantity <
                        $cartItem['quantity']
                    ) {

                        throw new \Exception(
                            "Stok '{$dbItem->name}' tidak mencukupi."
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CONSUMABLE / MERCHANDISE
                    | Permanently reduce stock
                    |--------------------------------------------------------------------------
                    */

                    $dbItem->decrement(
                        'stock_quantity',
                        $cartItem['quantity']
                    );

                }

            }


            /*
            |--------------------------------------------------------------------------
            | ORDER
            |--------------------------------------------------------------------------
            */

            $order =
                Order::create([

                    'order_number' =>
                        'ORD-' .
                        strtoupper(
                            uniqid()
                        ),

                    'user_id' =>
                        auth()->id(),

                    'full_name' =>
                        $request->full_name,

                    'organization' =>
                        $request->organization,

                    'position' =>
                        $request->position,

                    'phone_number' =>
                        $request->phone_number,

                    'proker_name' =>
                        $request->proker_name,

                    'ketua_acara' =>
                        $request->ketua_acara,

                    'department' =>
                        '-',

                    'treasurer_name' =>
                        $request->treasurer_name,

                    'address' =>
                        $request->address,

                    'notes' =>
                        $request->notes,

                    'order_type' =>
                        $orderType,

                    'start_date' =>
                        $firstItem[
                            'start_date'
                        ],

                    'start_time' =>
                        $firstItem[
                            'start_time'
                        ],

                    'end_date' =>
                        $firstItem[
                            'end_date'
                        ] ?? null,

                    'end_time' =>
                        $firstItem[
                            'end_time'
                        ] ?? null,

                    'is_sop_accepted' =>
                        true,

                    'status' =>
                        'Pending',

            

                ]);


            /*
            |--------------------------------------------------------------------------
            | ORDER ITEMS
            |--------------------------------------------------------------------------
            */

            foreach ($cart as $cartItem) {

                $unitPrice =
                    (int) (
                        $cartItem['price']
                        ?? 0
                    );


                $unitPrice +=
                    (int) (
                        $cartItem[
                            'size_additional_price'
                        ]
                        ?? 0
                    );


                if (
                    $request->organization ===
                    'Student Council' &&
                    $orderType ===
                    'Habis Pakai'
                ) {

                    $unitPrice = 0;

                }


                $subtotal =
                    $unitPrice *
                    (int) $cartItem['quantity'];


                OrderItem::create([

                    'order_id' =>
                        $order->id,

                    'item_id' =>
                        $cartItem['id'],

                    'quantity' =>
                        $cartItem['quantity'],

                    'size' =>
                        $cartItem['size']
                        ?? null,

                    'design_link' =>
                        $cartItem['design_link']
                        ?? null,

                    'size_additional_price' =>
                        $cartItem[
                            'size_additional_price'
                        ] ?? 0,

                    'subtotal_price' =>
                        $subtotal,

                ]);

            }


            /*
            |--------------------------------------------------------------------------
            | MOU DOCUMENTS
            |--------------------------------------------------------------------------
            */

            $mouTypes = [];


            foreach ($cart as $cartItem) {

                $mouType =
                    $this->getMouType(
                        $cartItem
                    );


                if ($mouType) {

                    $mouTypes[
                        $mouType
                    ] = true;

                }

            }


            foreach (
                array_keys($mouTypes)
                as $mouType
            ) {

                OrderMouDocument::create([

                    'order_id' =>
                        $order->id,

                    'mou_type' =>
                        $mouType,

                ]);

            }


            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            if (!empty($mouTypes)) {

                $order->update([

                    'status' =>
                        'Waiting for MoU',

                ]);

            } else {

                $order->update([

                    'status' =>
                        'Waiting for Payment',

                ]);

            }


            DB::commit();


            session()->forget(
                'cart'
            );


            $this->notifyAdmins(
                "Transaksi baru masuk: {$order->order_number}"
            );


            return redirect()
                ->route(
                    'student.loans'
                )
                ->with(
                    'success',
                    'Pesanan berhasil dibuat!'
                );


        } catch (\Throwable $e) {

            DB::rollBack();


            return back()->with(
                'error',
                $e->getMessage()
            );

        }
    }


    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    private function requiresReturn(
        string $transactionType
    ): bool {

        return in_array(
            $transactionType,
            [
                'Peralatan',
                'Handy Talkie',
            ],
            true
        );

    }


    private function getMouType(
        array $item
    ): ?string {

        $requiresMou =
            in_array(
                $item['requires_mou'] ?? false,
                [
                    true,
                    1,
                    '1',
                ],
                true
            );


        if (!$requiresMou) {
            return null;
        }


        $transactionType =
            $item[
                'transaction_type'
            ] ?? null;


        /*
        |--------------------------------------------------------------------------
        | HANDY TALKIE
        |--------------------------------------------------------------------------
        */

        if (
            $transactionType ===
            'Handy Talkie'
        ) {

            return 'ht';

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
                ($item[
                    'transaction_detail'
                ] ?? null)
                ===
                'Vendor Rental'
            ) {

                return 'vendor';

            }


            return 'internal';

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

            $subcategory =
                $item[
                    'subcategory'
                ] ?? null;


            if (
                $subcategory ===
                'Baju'
            ) {

                return 'merch_baju';

            }


            if (
                $subcategory ===
                'ID Card'
            ) {

                return 'merch_idcard';

            }

        }


        return null;
    }


    private function isValidTime(
        string $time
    ): bool {

        return in_array(
            $time,
            self::TIME_OPTIONS,
            true
        );

    }


    private function getSizeAdditionalPrice(
        ?string $size
    ): int {

        return match ($size) {

            '2XL' =>
                5000,

            '3XL' =>
                10000,

            '4XL' =>
                15000,

            '5XL' =>
                20000,

            default =>
                0,

        };

    }


   private function buildLineKey(
    Item $item,
    ?string $size,
    ?string $designLink = null
): string {

    $designKey = 'none';

    if ($designLink) {
        $designKey = md5($designLink);
    }

    return implode(
        '_',
        [
            $item->id,
            $item->subcategory ?? 'none',
            $size ?? 'none',
            $designKey,
        ]
    );
}


    private function getOverlappingQuantity(
        int $itemId,
        string $startDate,
        string $startTime,
        ?string $endDate,
        ?string $endTime
    ): int {

        if (
            !$endDate ||
            !$endTime
        ) {

            return 0;

        }


        $orders =
            OrderItem::where(
                'item_id',
                $itemId
            )
            ->whereHas(
                'order',
                function ($query) {

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
            ->with('order')
            ->get();


        $requestedStart =
            Carbon::parse(
                "{$startDate} {$startTime}"
            );


        $requestedEnd =
            Carbon::parse(
                "{$endDate} {$endTime}"
            );


        $total = 0;


        foreach (
            $orders
            as $orderItem
        ) {

            $order =
                $orderItem->order;


            if (
                !$order ||
                !$order->start_date
            ) {

                continue;

            }


            /*
            |--------------------------------------------------------------------------
            | OLD ORDERS WITHOUT TIME
            |--------------------------------------------------------------------------
            */

            if (
                !$order->start_time ||
                !$order->end_date ||
                !$order->end_time
            ) {

                $legacyStart =
                    Carbon::parse(
                        $order->start_date
                    )->startOfDay();


                $legacyEnd =
                    Carbon::parse(
                        $order->end_date ??
                        $order->start_date
                    )->endOfDay();


                if (
                    $requestedStart <=
                        $legacyEnd &&
                    $requestedEnd >=
                        $legacyStart
                ) {

                    $total +=
                        (int) $orderItem->quantity;

                }


                continue;

            }


            $existingStart =
                Carbon::parse(
                    $order->start_date .
                    ' ' .
                    $order->start_time
                );


            $existingEnd =
                Carbon::parse(
                    $order->end_date .
                    ' ' .
                    $order->end_time
                );


            if (
                $requestedStart <
                    $existingEnd &&
                $requestedEnd >
                    $existingStart
            ) {

                $total +=
                    (int) $orderItem->quantity;

            }

        }


        return $total;
    }


    private function notifyAdmins(
        string $message
    ): void {

        $admins =
            User::where(
                'role',
                'admin'
            )->get();


        foreach (
            $admins
            as $admin
        ) {

            $admin->notify(
                new AdminNotification(
                    $message
                )
            );

        }

    }
}