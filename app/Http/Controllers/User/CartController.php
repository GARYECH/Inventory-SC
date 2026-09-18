<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemSize;
use App\Models\OrderMouDocument;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\AdminNotification;
use App\Services\InventoryAvailabilityService;
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

    private const BAJU_SIZES = [
        'S',
        'M',
        'L',
        'XL',
        '2XL',
        '3XL',
        '4XL',
        '5XL',
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

        $colorCharts = [];

        $colorChartSetting =
            Setting::where(
                'key',
                'baju_color_charts'
            )->value(
                'value'
            );

        if ($colorChartSetting) {

            $decoded =
                json_decode(
                    $colorChartSetting,
                    true
                );

            if (
                is_array(
                    $decoded
                )
            ) {
                $colorCharts =
                    $decoded;
            }
        }

        return view(
            'user.cart',
            compact(
                'cart',
                'colorCharts'
            )
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

        $cart =
            session()->get(
                'cart',
                []
            );

        /*
        |--------------------------------------------------------------------------
        | BAJU → DEDICATED PAGE
        |--------------------------------------------------------------------------
        */

        if (
            $transactionType ===
            'Merchandise'
            &&
            $item->subcategory ===
            'Baju'
        ) {

            if (!empty($cart)) {

                $firstItem =
                    reset($cart);

                if (
                    (
                        $firstItem[
                            'transaction_type'
                        ] ?? null
                    )
                    !==
                    'Merchandise'
                ) {
                    return back()->with(
                        'error',
                        'Satu transaksi hanya dapat berisi barang dengan Transaction Type yang sama.'
                    );
                }
            }

            return redirect()->route(
                'student.cart.baju.create',
                $item->id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SAME TRANSACTION TYPE
        |--------------------------------------------------------------------------
        */

        if (!empty($cart)) {

            $firstItem =
                reset($cart);

            if (
                (
                    $firstItem[
                        'transaction_type'
                    ] ?? null
                )
                !==
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
        | BASIC VALIDATION
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
                            'Jam transaksi hanya boleh 17:00, 17:30, 18:00, 18:30, atau 19:00.'
                        );
                    }

                },
            ],
        ]);

        $quantity =
            (int) $request->quantity;

        $startDate =
            $request->start_date;

        $startTime =
            $request->start_time;

        $endDate =
            null;

        $endTime =
            null;

        /*
        |--------------------------------------------------------------------------
        | MERCHANDISE NON-BAJU
        |--------------------------------------------------------------------------
        */

        $size =
            null;

        $designLink =
            null;

        $sizeAdditionalPrice =
            0;

        if (
            $transactionType ===
            'Merchandise'
        ) {

            if (
                !in_array(
                    $item->subcategory,
                    [
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

            if (
                $item->subcategory ===
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
        | RENTAL
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
                                'Jam pengembalian hanya boleh 17:00, 17:30, 18:00, 18:30, atau 19:00.'
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

            $firstItem =
                reset($cart);

            if (
                (
                    $firstItem[
                        'start_date'
                    ] ?? null
                )
                !==
                $startDate
                ||
                (
                    $firstItem[
                        'start_time'
                    ] ?? null
                )
                !==
                $startTime
            ) {
                return back()->with(
                    'error',
                    'Semua barang dalam satu transaksi harus menggunakan tanggal dan jam pengambilan yang sama.'
                );
            }

            if ($isRental) {

                if (
                    (
                        $firstItem[
                            'end_date'
                        ] ?? null
                    )
                    !==
                    $endDate
                    ||
                    (
                        $firstItem[
                            'end_time'
                        ] ?? null
                    )
                    !==
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
                $size,
                $designLink
            );

        $existingQuantity =
            (int) (
                $cart[
                    $lineKey
                ]['quantity']
                ?? 0
            );

        $cartQuantityForSameItem =
            $this->getCartQuantityForItem(
                $cart,
                $item->id
            );

        /*
        |--------------------------------------------------------------------------
        | STOCK CHECK
        |--------------------------------------------------------------------------
        |
        | Physical stock tidak dikurangi
        | ketika masuk Cart.
        |
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
                $overlappingQty
                +
                $cartQuantityForSameItem
                +
                $quantity;

            if (
                $totalRequested
                >
                (int) $item->stock_quantity
            ) {

                $remaining =
                    max(
                        0,
                        (int) $item->stock_quantity
                        -
                        $overlappingQty
                        -
                        $cartQuantityForSameItem
                    );

                return back()->with(
                    'error',
                    "Gagal! Sisa stok '{$item->name}' untuk jadwal tersebut hanya {$remaining} unit."
                );
            }

        } else {

            $totalRequested =
                $cartQuantityForSameItem
                +
                $quantity;

            if (
                $totalRequested
                >
                (int) $item->stock_quantity
            ) {

                $remaining =
                    max(
                        0,
                        (int) $item->stock_quantity
                        -
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
        | MOU
        |--------------------------------------------------------------------------
        */

        $requiresMou =
            (bool) $item->requires_mou;

        if (
            $transactionType ===
            'Merchandise'
            &&
            $item->subcategory ===
            'Lainnya'
        ) {
            $requiresMou =
                false;
        }

        /*
        |--------------------------------------------------------------------------
        | SAVE TO CART
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
                $existingQuantity + $quantity,

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

            'color_number' =>
                $cart[
                    $lineKey
                ]['color_number']
                ?? null,

            'design_link' =>
                $designLink,

            'size_additional_price' =>
                $sizeAdditionalPrice,

            'size_breakdowns' =>
                $cart[
                    $lineKey
                ]['size_breakdowns']
                ?? [],
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
    | BAJU FORM
    |--------------------------------------------------------------------------
    */

    public function bajuForm(
        Request $request,
        Item $item
    ) {
        if (
            $item->transaction_type !==
            'Merchandise'
            ||
            $item->subcategory !==
            'Baju'
        ) {
            return redirect()
                ->route(
                    'student.dashboard'
                )
                ->with(
                    'error',
                    'Form ini hanya tersedia untuk Merchandise → Baju.'
                );
        }

        $cart =
            session()->get(
                'cart',
                []
            );

        /*
        |--------------------------------------------------------------------------
        | SAME TRANSACTION TYPE
        |--------------------------------------------------------------------------
        */

        if (!empty($cart)) {

            $firstItem =
                reset($cart);

            if (
                (
                    $firstItem[
                        'transaction_type'
                    ] ?? null
                )
                !==
                'Merchandise'
            ) {
                return redirect()
                    ->route(
                        'student.dashboard'
                    )
                    ->with(
                        'error',
                        'Satu transaksi hanya dapat berisi barang dengan Transaction Type yang sama.'
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | EDIT DATA
        |--------------------------------------------------------------------------
        */

        $editLineKey =
            $request->query(
                'edit'
            );

        $editingCartItem =
            null;

        if (
            $editLineKey !== null
            &&
            isset(
                $cart[
                    $editLineKey
                ]
            )
        ) {

            $candidate =
                $cart[
                    $editLineKey
                ];

            if (
                (int) (
                    $candidate['id'] ?? 0
                )
                ===
                (int) $item->id
                &&
                (
                    $candidate[
                        'transaction_type'
                    ] ?? null
                )
                ===
                'Merchandise'
                &&
                (
                    $candidate[
                        'subcategory'
                    ] ?? null
                )
                ===
                'Baju'
            ) {

                $editingCartItem =
                    $candidate;

            } else {

                $editLineKey =
                    null;
            }

        } elseif (
            $editLineKey !== null
        ) {

            $editLineKey =
                null;
        }

        /*
        |--------------------------------------------------------------------------
        | PREFILL SCHEDULE
        |--------------------------------------------------------------------------
        */

        $prefillStartDate =
            null;

        $prefillStartTime =
            null;

        if (
            $editingCartItem !== null
        ) {

            $prefillStartDate =
                $editingCartItem[
                    'start_date'
                ] ?? null;

            $prefillStartTime =
                $editingCartItem[
                    'start_time'
                ] ?? null;

        } elseif (!empty($cart)) {

            $firstItem =
                reset($cart);

            $prefillStartDate =
                $firstItem[
                    'start_date'
                ] ?? null;

            $prefillStartTime =
                $firstItem[
                    'start_time'
                ] ?? null;
        }

        /*
        |--------------------------------------------------------------------------
        | PREFILL BAJU DATA
        |--------------------------------------------------------------------------
        */

        $prefillDesignLink =
            $editingCartItem[
                'design_link'
            ] ?? '';

        $prefillBreakdowns =
            $editingCartItem[
                'size_breakdowns'
            ] ?? [];

        /*
        |--------------------------------------------------------------------------
        | COLOR IS SELECTED IN CART
        |--------------------------------------------------------------------------
        */

        $colorCharts =
            [];

        /*
        |--------------------------------------------------------------------------
        | BAJU SIZES
        |--------------------------------------------------------------------------
        */

        $sizeOptions =
            self::BAJU_SIZES;

        $sizePrices =
            [];

        foreach (
            $sizeOptions as $size
        ) {

            $sizePrices[$size] =
                (int) $item->price
                +
                $this->getSizeAdditionalPrice(
                    $size
                );
        }

        return view(
            'user.baju_order',
            [
                'item' =>
                    $item,

                'timeOptions' =>
                    self::TIME_OPTIONS,

                'colorCharts' =>
                    $colorCharts,

                'prefillStartDate' =>
                    $prefillStartDate,

                'prefillStartTime' =>
                    $prefillStartTime,

                'prefillDesignLink' =>
                    $prefillDesignLink,

                'prefillBreakdowns' =>
                    $prefillBreakdowns,

                'editLineKey' =>
                    $editLineKey,

                'sizeOptions' =>
                    $sizeOptions,

                'sizePrices' =>
                    $sizePrices,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STORE BAJU
    |--------------------------------------------------------------------------
    */

    public function storeBaju(
        Request $request,
        Item $item
    ) {
        if (
            $item->transaction_type !==
            'Merchandise'
            ||
            $item->subcategory !==
            'Baju'
        ) {
            return back()->with(
                'error',
                'Form ini hanya tersedia untuk Merchandise → Baju.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | sizes sekarang berupa ARRAY OF ROWS.
        |
        | Contoh:
        |
        | sizes[0][size] = S
        | sizes[0][division] = Event
        | sizes[0][quantity] = 10
        |
        | sizes[1][size] = S
        | sizes[1][division] = Konsumsi
        | sizes[1][quantity] = 5
        |
        */

        $request->validate([
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
                            'Jam transaksi hanya boleh 17:00, 17:30, 18:00, 18:30, atau 19:00.'
                        );
                    }

                },
            ],

            'design_link' => [
                'required',
                'url',
                'max:2000',
            ],

            'sizes' => [
                'required',
                'array',
                'min:1',
            ],

            'sizes.*.size' => [
                'required',
                'string',
                'in:S,M,L,XL,2XL,3XL,4XL,5XL',
            ],

            'sizes.*.division' => [
                'required',
                'string',
                'max:255',
            ],

            'sizes.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'edit_line_key' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $cart =
            session()->get(
                'cart',
                []
            );

        /*
        |--------------------------------------------------------------------------
        | SAME TRANSACTION TYPE
        |--------------------------------------------------------------------------
        */

        if (!empty($cart)) {

            $firstItem =
                reset($cart);

            if (
                (
                    $firstItem[
                        'transaction_type'
                    ] ?? null
                )
                !==
                'Merchandise'
            ) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Satu transaksi hanya dapat berisi barang dengan Transaction Type yang sama.'
                    );
            }

            if (
                (
                    $firstItem[
                        'start_date'
                    ] ?? null
                )
                !==
                $request->start_date
                ||
                (
                    $firstItem[
                        'start_time'
                    ] ?? null
                )
                !==
                $request->start_time
            ) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Semua barang dalam satu transaksi harus menggunakan tanggal dan jam pengambilan yang sama.'
                    );
            }
        }

        $designLink =
            trim(
                $request->design_link
            );

        /*
        |--------------------------------------------------------------------------
        | EDIT LINE KEY
        |--------------------------------------------------------------------------
        */

        $editLineKey =
            $request->input(
                'edit_line_key'
            );

        if (
            $editLineKey !== null
            &&
            isset(
                $cart[
                    $editLineKey
                ]
            )
        ) {

            $editing =
                $cart[
                    $editLineKey
                ];

            if (
                (int) (
                    $editing['id'] ?? 0
                )
                !==
                (int) $item->id
                ||
                (
                    $editing[
                        'transaction_type'
                    ] ?? null
                )
                !==
                'Merchandise'
                ||
                (
                    $editing[
                        'subcategory'
                    ] ?? null
                )
                !==
                'Baju'
            ) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Data Baju yang akan diedit tidak valid.'
                    );
            }

        } else {

            $editLineKey =
                null;
        }

        /*
        |--------------------------------------------------------------------------
        | BUILD BREAKDOWNS
        |--------------------------------------------------------------------------
        */

        $breakdowns =
            [];

        foreach (
            $request->input(
                'sizes',
                []
            ) as $row
        ) {

            $size =
                trim(
                    (string) (
                        $row['size'] ?? ''
                    )
                );

            $division =
                trim(
                    (string) (
                        $row['division'] ?? ''
                    )
                );

            $quantity =
                (int) (
                    $row['quantity'] ?? 0
                );

            if (
                !in_array(
                    $size,
                    self::BAJU_SIZES,
                    true
                )
            ) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        "Ukuran '{$size}' tidak valid."
                    );
            }

            if (
                $division === ''
            ) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        "Divisi untuk ukuran {$size} wajib diisi."
                    );
            }

            if (
                $quantity < 1
            ) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        "Jumlah untuk ukuran {$size} harus lebih dari 0."
                    );
            }

            $additionalPrice =
                $this->getSizeAdditionalPrice(
                    $size
                );

            $unitPrice =
                (int) $item->price
                +
                $additionalPrice;

            /*
            |----------------------------------------------------------------------
            | DO NOT MERGE SAME SIZE
            |----------------------------------------------------------------------
            |
            | S + Event
            | S + Konsumsi
            |
            | harus tetap menjadi dua row berbeda.
            |
            */

            $breakdowns[] = [

                'size' =>
                    $size,

                'division' =>
                    $division,

                'quantity' =>
                    $quantity,

                'unit_price' =>
                    $unitPrice,

                'size_additional_price' =>
                    $additionalPrice,

                'subtotal_price' =>
                    $unitPrice *
                    $quantity,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | AT LEAST ONE SIZE
        |--------------------------------------------------------------------------
        */

        if (
            empty($breakdowns)
        ) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Isi minimal satu ukuran Baju dengan jumlah lebih dari 0.'
                );
        }

        $totalQuantity =
            collect(
                $breakdowns
            )->sum(
                'quantity'
            );

        /*
        |--------------------------------------------------------------------------
        | FIND EXISTING LINE WITH SAME ITEM + DESIGN
        |--------------------------------------------------------------------------
        */

        $existingLineKey =
            null;

        foreach (
            $cart as $lineKey =>
            $cartItem
        ) {

            if (
                $editLineKey !== null
                &&
                $lineKey ===
                $editLineKey
            ) {
                continue;
            }

            if (
                (int) (
                    $cartItem['id'] ?? 0
                )
                !==
                (int) $item->id
            ) {
                continue;
            }

            if (
                (
                    $cartItem[
                        'transaction_type'
                    ] ?? null
                )
                !==
                'Merchandise'
            ) {
                continue;
            }

            if (
                (
                    $cartItem[
                        'subcategory'
                    ] ?? null
                )
                !==
                'Baju'
            ) {
                continue;
            }

            $existingDesign =
                trim(
                    (string) (
                        $cartItem[
                            'design_link'
                        ] ?? ''
                    )
                );

            if (
                $existingDesign
                !==
                $designLink
            ) {
                continue;
            }

            $existingLineKey =
                $lineKey;

            break;
        }

        /*
        |--------------------------------------------------------------------------
        | STOCK CHECK
        |--------------------------------------------------------------------------
        |
        | Cart tidak melakukan reservation.
        |
        | Hanya pastikan total quantity Baju
        | di cart tidak melebihi physical stock.
        |
        */

        $otherCartQuantity =
            $this->getCartQuantityForItem(
                $cart,
                $item->id,
                $editLineKey
            );

        if (
            $otherCartQuantity
            +
            $totalQuantity
            >
            (int) $item->stock_quantity
        ) {

            $remaining =
                max(
                    0,
                    (int) $item->stock_quantity
                    -
                    $otherCartQuantity
                );

            return back()
                ->withInput()
                ->with(
                    'error',
                    "Gagal! Sisa stok '{$item->name}' hanya {$remaining} unit."
                );
        }

        /*
        |--------------------------------------------------------------------------
        | DETERMINE FINAL LINE KEY
        |--------------------------------------------------------------------------
        */

        if (
            $editLineKey !== null
        ) {

            $lineKey =
                $editLineKey;

        } elseif (
            $existingLineKey !== null
        ) {

            $lineKey =
                $existingLineKey;

        } else {

            $lineKey =
                $this->buildLineKey(
                    $item,
                    null,
                    $designLink
                );
        }

        /*
        |--------------------------------------------------------------------------
        | KEEP EXISTING COLOR
        |--------------------------------------------------------------------------
        */

        $colorNumber =
            $cart[
                $lineKey
            ]['color_number']
            ?? null;

        /*
        |--------------------------------------------------------------------------
        | SAVE BAJU
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | quantity =
        | sum semua breakdown.
        |
        | size_breakdowns =
        | semua row apa adanya.
        |
        */

        $cart[$lineKey] = [
            'id' =>
                $item->id,

            'name' =>
                $item->name,

            'price' =>
                (int) $item->price,

            'quantity' =>
                $totalQuantity,

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
                (bool) $item->requires_mou,

            'start_date' =>
                $request->start_date,

            'start_time' =>
                $request->start_time,

            'end_date' =>
                null,

            'end_time' =>
                null,

            'size' =>
                null,

            'color_number' =>
                $colorNumber,

            'design_link' =>
                $designLink,

            'size_additional_price' =>
                0,

            'size_breakdowns' =>
                array_values(
                    $breakdowns
                ),
        ];

        session()->put(
            'cart',
            $cart
        );

        return redirect()
            ->route(
                'student.cart.index'
            )
            ->with(
                'success',
                $editLineKey !== null
                    ? 'Detail ukuran Baju berhasil diubah.'
                    : 'Detail ukuran Baju berhasil masuk keranjang.'
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
    | UPDATE NORMAL CART ITEM
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

        /*
        |--------------------------------------------------------------------------
        | BAJU USES DEDICATED EDIT PAGE
        |--------------------------------------------------------------------------
        */

        if (
            (
                $cart[$lineKey][
                    'transaction_type'
                ] ?? null
            )
            ===
            'Merchandise'
            &&
            (
                $cart[$lineKey][
                    'subcategory'
                ] ?? null
            )
            ===
            'Baju'
        ) {
            return back()->with(
                'error',
                'Untuk Baju, ubah jumlah melalui Detail Pesanan Baju.'
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

        $isRental =
            $this->requiresReturn(
                $item->transaction_type
            );

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
                        $cart[$lineKey][
                            'start_date'
                        ],
                        $cart[$lineKey][
                            'start_time'
                        ],
                        $cart[$lineKey][
                            'end_date'
                        ],
                        $cart[$lineKey][
                            'end_time'
                        ]
                    );

            $totalRequested =
                $overlappingQty
                +
                $otherCartQuantity
                +
                $quantity;

            if (
                $totalRequested
                >
                (int) $item->stock_quantity
            ) {

                $remaining =
                    max(
                        0,
                        (int) $item->stock_quantity
                        -
                        $overlappingQty
                        -
                        $otherCartQuantity
                    );

                return back()->with(
                    'error',
                    "Gagal! Sisa stok '{$item->name}' untuk jadwal tersebut hanya {$remaining} unit."
                );
            }

        } else {

            $totalRequested =
                $otherCartQuantity
                +
                $quantity;

            if (
                $totalRequested
                >
                (int) $item->stock_quantity
            ) {

                $remaining =
                    max(
                        0,
                        (int) $item->stock_quantity
                        -
                        $otherCartQuantity
                    );

                return back()->with(
                    'error',
                    "Gagal! Sisa stok '{$item->name}' hanya {$remaining} unit."
                );
            }
        }

        $cart[
            $lineKey
        ]['quantity'] =
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
    | UPDATE BAJU COLOR
    |--------------------------------------------------------------------------
    */

    public function updateColor(
        Request $request,
        $itemId
    ) {
        $cart =
            session()->get(
                'cart',
                []
            );

        $item =
            Item::findOrFail(
                $itemId
            );

        if (
            $item->transaction_type !==
            'Merchandise'
            ||
            $item->subcategory !==
            'Baju'
        ) {
            return back()->with(
                'error',
                'Pilihan warna hanya tersedia untuk Merchandise → Baju.'
            );
        }

        $request->validate([
            'color_number' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        $colorNumber =
            trim(
                $request->color_number
            );

        if (
            $colorNumber === ''
        ) {
            return back()->with(
                'error',
                'Warna Baju tidak boleh kosong.'
            );
        }

        $found =
            false;

        foreach (
            $cart as $lineKey =>
            $cartItem
        ) {

            if (
                (int) (
                    $cartItem['id'] ?? 0
                )
                !==
                (int) $itemId
            ) {
                continue;
            }

            if (
                (
                    $cartItem[
                        'transaction_type'
                    ] ?? null
                )
                !==
                'Merchandise'
            ) {
                continue;
            }

            if (
                (
                    $cartItem[
                        'subcategory'
                    ] ?? null
                )
                !==
                'Baju'
            ) {
                continue;
            }

            $cart[
                $lineKey
            ]['color_number'] =
                $colorNumber;

            $found =
                true;
        }

        if (!$found) {
            return back()->with(
                'error',
                'Baju tidak ditemukan di keranjang.'
            );
        }

        session()->put(
            'cart',
            $cart
        );

        return back()->with(
            'success',
            'Warna Baju berhasil disimpan.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE CART ITEM
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

            
        ]);

        $firstItem =
            reset($cart);

        $orderType =
            $firstItem[
                'transaction_type'
            ] ?? null;

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
        | CART CONSISTENCY
        |--------------------------------------------------------------------------
        */

        foreach (
            $cart as $cartItem
        ) {

            /*
            | Same transaction type
            */

            if (
                (
                    $cartItem[
                        'transaction_type'
                    ] ?? null
                )
                !==
                $orderType
            ) {
                return back()->with(
                    'error',
                    'Semua barang dalam satu transaksi harus memiliki Transaction Type yang sama.'
                );
            }

            /*
            | Same start schedule
            */

            if (
                (
                    $cartItem[
                        'start_date'
                    ] ?? null
                )
                !==
                (
                    $firstItem[
                        'start_date'
                    ] ?? null
                )
                ||
                (
                    $cartItem[
                        'start_time'
                    ] ?? null
                )
                !==
                (
                    $firstItem[
                        'start_time'
                    ] ?? null
                )
            ) {
                return back()->with(
                    'error',
                    'Semua barang dalam satu transaksi harus menggunakan tanggal dan jam pengambilan yang sama.'
                );
            }

            /*
            | Same return schedule for returnable
            */

            if (
                $this->requiresReturn(
                    $cartItem[
                        'transaction_type'
                    ]
                )
            ) {

                if (
                    (
                        $cartItem[
                            'end_date'
                        ] ?? null
                    )
                    !==
                    (
                        $firstItem[
                            'end_date'
                        ] ?? null
                    )
                    ||
                    (
                        $cartItem[
                            'end_time'
                        ] ?? null
                    )
                    !==
                    (
                        $firstItem[
                            'end_time'
                        ] ?? null
                    )
                ) {
                    return back()->with(
                        'error',
                        'Semua barang rental dalam satu transaksi harus menggunakan jadwal pengembalian yang sama.'
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | BAJU VALIDATION
            |--------------------------------------------------------------------------
            */

            if (
                (
                    $cartItem[
                        'transaction_type'
                    ] ?? null
                )
                ===
                'Merchandise'
                &&
                (
                    $cartItem[
                        'subcategory'
                    ] ?? null
                )
                ===
                'Baju'
            ) {

                if (
                    empty(
                        $cartItem[
                            'size_breakdowns'
                        ] ?? []
                    )
                ) {
                    return back()->with(
                        'error',
                        "Detail ukuran Baju '{$cartItem['name']}' belum lengkap."
                    );
                }

                if (
                    trim(
                        (string) (
                            $cartItem[
                                'color_number'
                            ] ?? ''
                        )
                    )
                    ===
                    ''
                ) {
                    return back()->with(
                        'error',
                        "Warna Baju '{$cartItem['name']}' belum dipilih."
                    );
                }
            }
        }

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | GROUP REQUESTED QUANTITY BY ITEM
            |--------------------------------------------------------------------------
            */

            $requestedQuantities =
                [];

            foreach (
                $cart as $cartItem
            ) {

                $itemId =
                    (int) $cartItem['id'];

                $requestedQuantities[
                    $itemId
                ] =
                    (
                        $requestedQuantities[
                            $itemId
                        ] ?? 0
                    )
                    +
                    (int) (
                        $cartItem[
                            'quantity'
                        ] ?? 0
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | LOCK ITEMS AND RE-CHECK STOCK
            |--------------------------------------------------------------------------
            */

            foreach (
                $requestedQuantities
                as $itemId =>
                $requestedQuantity
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

                if (
                    !in_array(
                        $dbItem->transaction_type,
                        self::TRANSACTION_TYPES,
                        true
                    )
                ) {
                    throw new \Exception(
                        "Transaction Type '{$dbItem->name}' tidak valid."
                    );
                }

                $sample =
                    null;

                foreach (
                    $cart as $cartItem
                ) {

                    if (
                        (int) $cartItem['id']
                        ===
                        (int) $itemId
                    ) {

                        $sample =
                            $cartItem;

                        break;
                    }
                }

                if (!$sample) {
                    throw new \Exception(
                        "Data transaksi '{$dbItem->name}' tidak valid."
                    );
                }

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

                    $overlappingQty =
                        $this->availabilityService
                            ->getOverlappingQuantity(
                                $dbItem->id,
                                $sample[
                                    'start_date'
                                ],
                                $sample[
                                    'start_time'
                                ],
                                $sample[
                                    'end_date'
                                ],
                                $sample[
                                    'end_time'
                                ]
                            );

                    if (
                        $overlappingQty
                        +
                        $requestedQuantity
                        >
                        (int) $dbItem->stock_quantity
                    ) {

                        $remaining =
                            max(
                                0,
                                (int) $dbItem->stock_quantity
                                -
                                $overlappingQty
                            );

                        throw new \Exception(
                            "Stok '{$dbItem->name}' tidak mencukupi untuk jadwal tersebut. Sisa stok: {$remaining} unit."
                        );
                    }

                    /*
                    | Physical stock tidak dikurangi.
                    */

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | NON-RETURNABLE
                    |--------------------------------------------------------------------------
                    |
                    | Checkout belum mengurangi physical stock.
                    |
                    | Stock baru berkurang:
                    |
                    | Pending -> Approved
                    |
                    */

                    if (
                        $requestedQuantity
                        >
                        (int) $dbItem->stock_quantity
                    ) {
                        throw new \Exception(
                            "Stok '{$dbItem->name}' tidak mencukupi. Stok tersedia: {$dbItem->stock_quantity} unit."
                        );
                    }
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

                $sizeBreakdowns =
                    $cartItem[
                        'size_breakdowns'
                    ] ?? [];

                $isBaju =
                    $orderType ===
                    'Merchandise'
                    &&
                    (
                        $cartItem[
                            'subcategory'
                        ] ?? null
                    )
                    ===
                    'Baju'
                    &&
                    !empty(
                        $sizeBreakdowns
                    );

                $basePrice =
                    (int) (
                        $cartItem[
                            'price'
                        ] ?? 0
                    );

                $sizeExtra =
                    (int) (
                        $cartItem[
                            'size_additional_price'
                        ] ?? 0
                    );

                /*
                |--------------------------------------------------------------------------
                | BAJU
                |--------------------------------------------------------------------------
                */

                if ($isBaju) {

                    $quantity =
                        collect(
                            $sizeBreakdowns
                        )->sum(
                            'quantity'
                        );

                    $subtotal =
                        collect(
                            $sizeBreakdowns
                        )->sum(
                            'subtotal_price'
                        );

                    $orderItemSize =
                        null;

                    $orderItemSizeExtra =
                        0;

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | STUDENT COUNCIL FREE HABIS PAKAI
                    |--------------------------------------------------------------------------
                    */

                    $unitPrice =
                        $basePrice;

                    if (
                        $request->organization ===
                        'Student Council'
                        &&
                        $orderType ===
                        'Habis Pakai'
                    ) {
                        $unitPrice =
                            0;
                    }

                    $unitPrice +=
                        $sizeExtra;

                    $quantity =
                        (int) (
                            $cartItem[
                                'quantity'
                            ] ?? 0
                        );

                    $subtotal =
                        $unitPrice *
                        $quantity;

                    $orderItemSize =
                        $cartItem[
                            'size'
                        ] ?? null;

                    $orderItemSizeExtra =
                        $sizeExtra;
                }

                /*
                |--------------------------------------------------------------------------
                | CREATE ORDER ITEM
                |--------------------------------------------------------------------------
                */

                $orderItem =
                    OrderItem::create([
                        'order_id' =>
                            $order->id,

                        'item_id' =>
                            $cartItem[
                                'id'
                            ],

                        'quantity' =>
                            $quantity,

                        'size' =>
                            $orderItemSize,

                        'color_number' =>
                            $cartItem[
                                'color_number'
                            ] ?? null,

                        'design_link' =>
                            $cartItem[
                                'design_link'
                            ] ?? null,

                        'size_additional_price' =>
                            $orderItemSizeExtra,

                        'subtotal_price' =>
                            $subtotal,
                    ]);

                /*
                |--------------------------------------------------------------------------
                | CREATE BAJU SIZE ROWS
                |--------------------------------------------------------------------------
                */

                if ($isBaju) {

                    foreach (
                        $sizeBreakdowns
                        as $breakdown
                    ) {

                        OrderItemSize::create([
                            'order_item_id' =>
                                $orderItem->id,

                            'size' =>
                                $breakdown[
                                    'size'
                                ],

                            'division' =>
                                $breakdown[
                                    'division'
                                ],

                            'quantity' =>
                                (int) (
                                    $breakdown[
                                        'quantity'
                                    ]
                                ),

                            'unit_price' =>
                                (int) (
                                    $breakdown[
                                        'unit_price'
                                    ]
                                ),

                            'size_additional_price' =>
                                (int) (
                                    $breakdown[
                                        'size_additional_price'
                                    ] ?? 0
                                ),

                            'subtotal_price' =>
                                (int) (
                                    $breakdown[
                                        'subtotal_price'
                                    ] ?? 0
                                ),
                        ]);
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | MOU DOCUMENTS
            |--------------------------------------------------------------------------
            */

            $mouTypes =
                [];

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
            | KEEP PENDING
            |--------------------------------------------------------------------------
            */

            $order->update([
                'status' =>
                    'Pending',
            ]);

            DB::commit();

            session()->forget(
                'cart'
            );

            /*
            |--------------------------------------------------------------------------
            | ADMIN NOTIFICATION
            |--------------------------------------------------------------------------
            */

            $this->notifyAdmins(
                "Pengajuan transaksi baru: {$order->order_number}"
            );

            return redirect()
                ->route(
                    'student.loans'
                )
                ->with(
                    'success',
                    'Pengajuan transaksi berhasil dibuat dan menunggu persetujuan admin.'
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
            filter_var(
                $item[
                    'requires_mou'
                ] ?? false,
                FILTER_VALIDATE_BOOLEAN
            );

        if (!$requiresMou) {
            return null;
        }

        $transactionType =
            $item[
                'transaction_type'
            ] ?? null;

        if (
            $transactionType ===
            'Handy Talkie'
        ) {
            return 'ht';
        }

        if (
            $transactionType ===
            'Peralatan'
        ) {

            return match (
                $item[
                    'transaction_detail'
                ] ?? null
            ) {

                'Internal Rental' =>
                    'internal',

                'Vendor Rental' =>
                    'vendor',

                default =>
                    null,
            };
        }

        if (
            $transactionType ===
            'Merchandise'
        ) {

            return match (
                $item[
                    'subcategory'
                ] ?? null
            ) {

                'Baju' =>
                    'merch_baju',

                'ID Card' =>
                    'merch_idcard',

                default =>
                    null,
            };
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

            'S',
            'M',
            'L',
            'XL' =>
                0,

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

        if ($designLink) {

            $designKey =
                md5(
                    trim(
                        $designLink
                    )
                );
        }

        /*
        |--------------------------------------------------------------------------
        | BAJU
        |--------------------------------------------------------------------------
        |
        | Satu line = satu item + satu design.
        |
        | Semua size berada di:
        |
        | size_breakdowns
        |
        */

        if (
            $item->transaction_type ===
            'Merchandise'
            &&
            $item->subcategory ===
            'Baju'
        ) {

            return implode(
                '_',
                [
                    $item->id,
                    'Baju',
                    $designKey,
                ]
            );
        }

        return implode(
            '_',
            [
                $item->id,

                $item->subcategory
                    ??
                    'none',

                $size
                    ??
                    'none',

                $designKey,
            ]
        );
    }

    private function getCartQuantityForItem(
        array $cart,
        int $itemId,
        ?string $exceptLineKey = null
    ): int {

        $total =
            0;

        foreach (
            $cart as $key =>
            $cartItem
        ) {

            if (
                $exceptLineKey !== null
                &&
                $key ===
                $exceptLineKey
            ) {
                continue;
            }

            if (
                (int) (
                    $cartItem[
                        'id'
                    ] ?? 0
                )
                ===
                $itemId
            ) {

                $total +=
                    (int) (
                        $cartItem[
                            'quantity'
                        ] ?? 0
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