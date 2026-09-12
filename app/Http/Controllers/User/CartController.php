<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderMouDocument;
use App\Models\User;
use App\Notifications\AdminNotification;
use App\Services\InventoryAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    private InventoryAvailabilityService $availabilityService;

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

    public function __construct(
        InventoryAvailabilityService $availabilityService
    ) {
        $this->availabilityService =
            $availabilityService;
    }


    /*
    |--------------------------------------------------------------------------
    | VIEW CART
    |--------------------------------------------------------------------------
    */

    public function viewCart()
    {
        $cart =
            session()->get(
                'cart',
                []
            );

        return view(
            'user.cart',
            compact('cart')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ADD TO CART
    |--------------------------------------------------------------------------
    */

    public function addToCart(
        Request $request,
        Item $item
    ) {
        $cart =
            session()->get(
                'cart',
                []
            );

        $requestQuantity =
            (int) (
                $request->quantity
                ?? 1
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDATE TRANSACTION TYPE
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $item->transaction_type,
                self::TRANSACTION_TYPES,
                true
            )
        ) {
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

            $firstItem =
                reset($cart);

            $firstTransactionType =
                $firstItem[
                    'transaction_type'
                ]
                ?? null;

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
        | MERCHANDISE DATA
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
                    trim(
                        $request->design_link
                    );

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
                    trim(
                        $request->design_link
                    );
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

                    if (
                        !$this->isValidTime(
                            $value
                        )
                    ) {
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

                        if (
                            !$this->isValidTime(
                                $value
                            )
                        ) {
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


            if (
                $isRental
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
        |
        | Design link ikut menjadi bagian dari key.
        | Jadi:
        |
        | Baju + M + Design A
        | Baju + M + Design B
        |
        | dianggap sebagai 2 line berbeda.
        |
        */

        $lineKey =
            $this->buildLineKey(
                $item,
                $size,
                $designLink
            );


        $existingQuantity =
            (int) (
                $cart[$lineKey]['quantity']
                ?? 0
            );


        /*
        |--------------------------------------------------------------------------
        | CURRENT CART QUANTITY FOR SAME ITEM
        |--------------------------------------------------------------------------
        |
        | Penting untuk mencegah:
        |
        | Baju Design A = 10
        | Baju Design B = 10
        |
        | padahal stock cuma 15.
        |
        */

        $cartQuantityForSameItem =
            $this->getCartQuantityForItem(
                $cart,
                $item->id
            );


        /*
        |--------------------------------------------------------------------------
        | STOCK CHECK
        |--------------------------------------------------------------------------
        */

        if ($isRental) {

            $overlappingQty =
                $this->availabilityService
                    ->getOverlappingQuantity(
                        $item->id,
                        $startDate,
                        $startTime,
                        $endDate,
                        $endTime
                    );


            $totalRequested =
                $overlappingQty +
                $cartQuantityForSameItem +
                $requestQuantity;


            if (
                $totalRequested >
                $item->stock_quantity
            ) {

                $remaining =
                    max(
                        0,
                        $item->stock_quantity -
                        $overlappingQty -
                        $cartQuantityForSameItem
                    );


                return back()->with(
                    'error',
                    "Gagal! Sisa stok '{$item->name}' untuk jadwal tersebut hanya {$remaining} unit."
                );
            }

        } else {

            $totalCartQuantity =
                $cartQuantityForSameItem -
                $existingQuantity +
                $existingQuantity +
                $requestQuantity;


            /*
             * Bentuk di atas sengaja disederhanakan
             * menjadi total seluruh cart + request.
             */
            $totalCartQuantity =
                $cartQuantityForSameItem +
                $requestQuantity;


            if (
                $totalCartQuantity >
                $item->stock_quantity
            ) {

                $remaining =
                    max(
                        0,
                        $item->stock_quantity -
                        $cartQuantityForSameItem
                    );


                return back()->with(
                    'error',
                    "Gagal! Sisa stok '{$item->name}' hanya {$remaining} unit."
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE MERCHANDISE
        |--------------------------------------------------------------------------
        */

        $requiresMou =
            (bool) $item->requires_mou;


        if (
            $transactionType ===
            'Merchandise' &&
            $item->subcategory ===
            'Lainnya'
        ) {
            $requiresMou = false;
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
                (int) $item->price,

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
                $requiresMou,

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


    /*
    |--------------------------------------------------------------------------
    | CLEAR CART
    |--------------------------------------------------------------------------
    */

    public function clearCart()
    {
        session()->forget(
            'cart'
        );

        return back()->with(
            'success',
            'Keranjang dikosongkan.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE CART
    |--------------------------------------------------------------------------
    */

    public function updateCart(
        Request $request,
        $lineKey
    ) {

        $cart =
            session()->get(
                'cart',
                []
            );


        if (
            !isset(
                $cart[$lineKey]
            )
        ) {
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


        if (
            !in_array(
                $transactionType,
                self::TRANSACTION_TYPES,
                true
            )
        ) {
            return back()->with(
                'error',
                'Transaction Type barang tidak valid.'
            );
        }


        $isRental =
            $this->requiresReturn(
                $transactionType
            );


        /*
        |--------------------------------------------------------------------------
        | CURRENT CART QUANTITY EXCEPT CURRENT LINE
        |--------------------------------------------------------------------------
        */

        $otherCartQuantity =
            $this->getCartQuantityForItem(
                $cart,
                $item->id,
                $lineKey
            );


        if ($isRental) {

            $overlappingQty =
                $this->availabilityService
                    ->getOverlappingQuantity(
                        $item->id,
                        $cart[$lineKey]['start_date'],
                        $cart[$lineKey]['start_time'],
                        $cart[$lineKey]['end_date'],
                        $cart[$lineKey]['end_time']
                    );


            $totalRequested =
                $overlappingQty +
                $otherCartQuantity +
                $quantity;


            if (
                $totalRequested >
                $item->stock_quantity
            ) {

                $remaining =
                    max(
                        0,
                        $item->stock_quantity -
                        $overlappingQty -
                        $otherCartQuantity
                    );


                return back()->with(
                    'error',
                    "Gagal! Sisa stok '{$item->name}' untuk jadwal tersebut hanya {$remaining} unit."
                );
            }

        } else {

            $totalRequested =
                $otherCartQuantity +
                $quantity;


            if (
                $totalRequested >
                $item->stock_quantity
            ) {

                $remaining =
                    max(
                        0,
                        $item->stock_quantity -
                        $otherCartQuantity
                    );


                return back()->with(
                    'error',
                    "Gagal! Sisa stok '{$item->name}' hanya {$remaining} unit."
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


    /*
    |--------------------------------------------------------------------------
    | REMOVE ITEM
    |--------------------------------------------------------------------------
    */

    public function removeItem(
        $lineKey
    ) {

        $cart =
            session()->get(
                'cart',
                []
            );


        if (
            isset(
                $cart[$lineKey]
            )
        ) {

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


    /*
    |--------------------------------------------------------------------------
    | PROCESS CHECKOUT
    |--------------------------------------------------------------------------
    */

    public function processCheckout(
        Request $request
    ) {

        $cart =
            session()->get(
                'cart',
                []
            );


        if (
            empty($cart)
        ) {
            return back()->with(
                'error',
                'Keranjangmu kosong!'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CHECKOUT FORM
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
        | FIRST CART ITEM
        |--------------------------------------------------------------------------
        */

        $firstItem =
            reset($cart);


        $orderType =
            $firstItem[
                'transaction_type'
            ]
            ?? null;


        if (
            !in_array(
                $orderType,
                self::TRANSACTION_TYPES,
                true
            )
        ) {
            return back()->with(
                'error',
                'Transaction Type tidak valid.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SAME TYPE + SAME SCHEDULE
        |--------------------------------------------------------------------------
        */

        foreach (
            $cart as $cartItem
        ) {

            if (
                ($cartItem[
                    'transaction_type'
                ] ?? null)
                !== $orderType
            ) {
                return back()->with(
                    'error',
                    'Semua barang dalam satu transaksi harus memiliki Transaction Type yang sama.'
                );
            }


            if (
                ($cartItem[
                    'start_date'
                ] ?? null)
                !==
                ($firstItem[
                    'start_date'
                ] ?? null)
                ||
                ($cartItem[
                    'start_time'
                ] ?? null)
                !==
                ($firstItem[
                    'start_time'
                ] ?? null)
            ) {
                return back()->with(
                    'error',
                    'Semua barang dalam satu transaksi harus menggunakan tanggal dan jam transaksi yang sama.'
                );
            }


            if (
                $this->requiresReturn(
                    $cartItem[
                        'transaction_type'
                    ]
                )
            ) {

                if (
                    ($cartItem[
                        'end_date'
                    ] ?? null)
                    !==
                    ($firstItem[
                        'end_date'
                    ] ?? null)
                    ||
                    ($cartItem[
                        'end_time'
                    ] ?? null)
                    !==
                    ($firstItem[
                        'end_time'
                    ] ?? null)
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
        | REVALIDATE CART DATA FROM DATABASE
        |--------------------------------------------------------------------------
        |
        | Jangan sepenuhnya percaya data yang tersimpan di session.
        | Item bisa saja sudah diedit Admin setelah barang masuk cart.
        |
        */

        foreach (
            $cart as $cartItem
        ) {

            $dbItem =
                Item::find(
                    $cartItem['id']
                );


            if (!$dbItem) {
                return back()->with(
                    'error',
                    "Barang '{$cartItem['name']}' sudah tidak tersedia."
                );
            }


            if (
                $dbItem->transaction_type !==
                ($cartItem[
                    'transaction_type'
                ] ?? null)
            ) {
                return back()->with(
                    'error',
                    "Transaction Type barang '{$dbItem->name}' sudah berubah. Silakan masukkan kembali barang ke keranjang."
                );
            }


            if (
                $dbItem->transaction_detail !==
                ($cartItem[
                    'transaction_detail'
                ] ?? null)
            ) {
                return back()->with(
                    'error',
                    "Transaction Detail barang '{$dbItem->name}' sudah berubah. Silakan masukkan kembali barang ke keranjang."
                );
            }


            if (
                $dbItem->subcategory !==
                ($cartItem[
                    'subcategory'
                ] ?? null)
            ) {
                return back()->with(
                    'error',
                    "Subcategory barang '{$dbItem->name}' sudah berubah. Silakan masukkan kembali barang ke keranjang."
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | MERCHANDISE REVALIDATION
        |--------------------------------------------------------------------------
        */

        foreach (
            $cart as $cartItem
        ) {

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


            if (
                $subcategory ===
                'Lainnya'
            ) {

                /*
                 * Lainnya tidak menggunakan MoU.
                 */
                $cartItem[
                    'requires_mou'
                ] = false;
            }
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
            | AGGREGATE CART QUANTITY BY ITEM
            |--------------------------------------------------------------------------
            |
            | Penting untuk kasus:
            |
            | Item A + Design A = 5
            | Item A + Design B = 5
            |
            | Stock tetap dihitung sebagai total 10.
            |
            */

            $requestedQuantities = [];


            foreach (
                $cart as $cartItem
            ) {

                $itemId =
                    (int) $cartItem['id'];


                if (
                    !isset(
                        $requestedQuantities[
                            $itemId
                        ]
                    )
                ) {

                    $requestedQuantities[
                        $itemId
                    ] = 0;
                }


                $requestedQuantities[
                    $itemId
                ] +=
                    (int) $cartItem['quantity'];
            }


            /*
            |--------------------------------------------------------------------------
            | FINAL STOCK CHECK
            |--------------------------------------------------------------------------
            */

            $lockedItems = [];


            foreach (
                $requestedQuantities
                as $itemId => $requestedQuantity
            ) {

                $dbItem =
                    Item::lockForUpdate()
                        ->find(
                            $itemId
                        );


                if (!$dbItem) {

                    throw new \Exception(
                        'Salah satu barang sudah tidak tersedia.'
                    );
                }


                $lockedItems[
                    $itemId
                ] =
                    $dbItem;


                /*
                |--------------------------------------------------------------------------
                | RETURNABLE
                |--------------------------------------------------------------------------
                */

                if (
                    $this->requiresReturn(
                        $dbItem->transaction_type
                    )
                ) {

                    $cartSample = null;


                    foreach (
                        $cart as $cartItem
                    ) {

                        if (
                            (int) $cartItem['id']
                            ===
                            (int) $itemId
                        ) {

                            $cartSample =
                                $cartItem;

                            break;
                        }
                    }


                    if (!$cartSample) {

                        throw new \Exception(
                            "Data transaksi '{$dbItem->name}' tidak valid."
                        );
                    }


                    $overlappingQty =
                        $this->availabilityService
                            ->getOverlappingQuantity(
                                $dbItem->id,
                                $cartSample[
                                    'start_date'
                                ],
                                $cartSample[
                                    'start_time'
                                ],
                                $cartSample[
                                    'end_date'
                                ],
                                $cartSample[
                                    'end_time'
                                ]
                            );


                    if (
                        $overlappingQty +
                        $requestedQuantity >
                        $dbItem->stock_quantity
                    ) {

                        throw new \Exception(
                            "Stok '{$dbItem->name}' tidak mencukupi untuk jadwal tersebut. Sisa stok saat ini adalah " .
                            max(
                                0,
                                $dbItem->stock_quantity -
                                $overlappingQty
                            ) .
                            ' unit.'
                        );
                    }


                    /*
                     * IMPORTANT:
                     *
                     * Returnable stock TIDAK
                     * di-decrement di database.
                     */
                }


                /*
                |--------------------------------------------------------------------------
                | NON-RETURNABLE
                |--------------------------------------------------------------------------
                */

                else {

                    if (
                        $requestedQuantity >
                        $dbItem->stock_quantity
                    ) {

                        throw new \Exception(
                            "Stok '{$dbItem->name}' tidak mencukupi. Stok tersedia: {$dbItem->stock_quantity} unit."
                        );
                    }


                    /*
                     * Habis Pakai / Merchandise
                     * mengurangi stock fisik.
                     */

                    $dbItem->decrement(
                        'stock_quantity',
                        $requestedQuantity
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | CREATE ORDER
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
            | CREATE ORDER ITEMS
            |--------------------------------------------------------------------------
            */

            foreach (
                $cart as $cartItem
            ) {

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


                $subtotal =
                    $unitPrice *
                    (int) $cartItem[
                        'quantity'
                    ];


                OrderItem::create([

                    'order_id' =>
                        $order->id,

                    'item_id' =>
                        $cartItem['id'],

                    'quantity' =>
                        (int) $cartItem[
                            'quantity'
                        ],

                    'size' =>
                        $cartItem['size']
                        ?? null,

                    'design_link' =>
                        $cartItem[
                            'design_link'
                        ]
                        ?? null,

                    'size_additional_price' =>
                        (int) (
                            $cartItem[
                                'size_additional_price'
                            ]
                            ?? 0
                        ),

                    'subtotal_price' =>
                        $subtotal,

                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | CREATE MOU DOCUMENTS
            |--------------------------------------------------------------------------
            */

            $mouTypes = [];


            foreach (
                $cart as $cartItem
            ) {

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
                array_keys(
                    $mouTypes
                ) as $mouType
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
            | FINAL ORDER STATUS
            |--------------------------------------------------------------------------
            */

            if (
                !empty(
                    $mouTypes
                )
            ) {

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


            /*
            |--------------------------------------------------------------------------
            | CLEAR CART
            |--------------------------------------------------------------------------
            */

            session()->forget(
                'cart'
            );


            /*
            |--------------------------------------------------------------------------
            | ADMIN NOTIFICATION
            |--------------------------------------------------------------------------
            */

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
                $item[
                    'requires_mou'
                ] ?? false,
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
            ]
            ?? null;


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


            if (
                ($item[
                    'transaction_detail'
                ] ?? null)
                ===
                'Internal Rental'
            ) {
                return 'internal';
            }


            return null;
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

        $designKey =
            'none';


        if (
            $designLink
        ) {

            $designKey =
                md5(
                    trim(
                        $designLink
                    )
                );
        }


        return implode(
            '_',
            [
                $item->id,
                $item->subcategory
                    ?? 'none',
                $size
                    ?? 'none',
                $designKey,
            ]
        );
    }


    private function getCartQuantityForItem(
        array $cart,
        int $itemId,
        ?string $exceptLineKey = null
    ): int {

        $total = 0;


        foreach (
            $cart as $key => $cartItem
        ) {

            if (
                $exceptLineKey !== null &&
                $key === $exceptLineKey
            ) {
                continue;
            }


            if (
                (int) (
                    $cartItem['id']
                    ?? 0
                )
                ===
                $itemId
            ) {

                $total +=
                    (int) (
                        $cartItem[
                            'quantity'
                        ]
                        ?? 0
                    );
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
            $admins as $admin
        ) {

            $admin->notify(
                new AdminNotification(
                    $message
                )
            );
        }
    }
}