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

    private InventoryAvailabilityService $availabilityService;

    public function __construct(
        InventoryAvailabilityService $availabilityService
    ) {
        $this->availabilityService =
            $availabilityService;
    }

    public function viewCart()
    {
        $cart = session()->get(
            'cart',
            []
        );

        return view(
            'user.cart',
            compact('cart')
        );
    }

    public function addToCart(
        Request $request,
        Item $item
    ) {
        $cart = session()->get(
            'cart',
            []
        );

        $requestQuantity = (int) (
            $request->quantity ?? 1
        );

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

        if (!empty($cart)) {
            $firstItem = reset($cart);

            $firstTransactionType =
                $firstItem['transaction_type']
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

        $startDate =
            $request->start_date;

        $startTime =
            $request->start_time;

        $endDate = null;
        $endTime = null;

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

        if (!empty($cart)) {
            $existing = reset($cart);

            if (
                ($existing['start_date'] ?? null)
                !== $startDate ||
                ($existing['start_time'] ?? null)
                !== $startTime
            ) {
                return back()->with(
                    'error',
                    'Semua barang dalam satu transaksi harus menggunakan tanggal dan jam pengambilan yang sama.'
                );
            }

            if ($isRental) {
                if (
                    ($existing['end_date'] ?? null)
                    !== $endDate ||
                    ($existing['end_time'] ?? null)
                    !== $endTime
                ) {
                    return back()->with(
                        'error',
                        'Semua barang rental dalam satu transaksi harus menggunakan jadwal pengembalian yang sama.'
                    );
                }
            }
        }

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

        $cartQuantityForSameItem =
            $this->getCartQuantityForItem(
                $cart,
                $item->id
            );

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
                $cartQuantityForSameItem +
                $requestQuantity;

            if (
                $totalRequested >
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
        $cart = session()->get(
            'cart',
            []
        );

        if (
            !isset($cart[$lineKey])
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

        $item = Item::findOrFail(
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
                $otherCartQuantity +
                $quantity;

            if (
                $totalRequested >
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
        $cart = session()->get(
            'cart',
            []
        );

        if (
            isset($cart[$lineKey])
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

    public function processCheckout(
        Request $request
    ) {
        $cart = session()->get(
            'cart',
            []
        );

        if (empty($cart)) {
            return back()->with(
                'error',
                'Keranjangmu kosong!'
            );
        }

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

        $firstItem = reset($cart);

        $orderType =
            $firstItem['transaction_type']
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

        foreach ($cart as $cartItem) {
            if (
                ($cartItem['transaction_type'] ?? null)
                !== $orderType
            ) {
                return back()->with(
                    'error',
                    'Semua barang dalam satu transaksi harus memiliki Transaction Type yang sama.'
                );
            }

            if (
                ($cartItem['start_date'] ?? null)
                !==
                ($firstItem['start_date'] ?? null)
                ||
                ($cartItem['start_time'] ?? null)
                !==
                ($firstItem['start_time'] ?? null)
            ) {
                return back()->with(
                    'error',
                    'Semua barang dalam satu transaksi harus menggunakan tanggal dan jam pengambilan yang sama.'
                );
            }

            if (
                $this->requiresReturn(
                    $cartItem['transaction_type']
                )
            ) {
                if (
                    ($cartItem['end_date'] ?? null)
                    !==
                    ($firstItem['end_date'] ?? null)
                    ||
                    ($cartItem['end_time'] ?? null)
                    !==
                    ($firstItem['end_time'] ?? null)
                ) {
                    return back()->with(
                        'error',
                        'Semua barang rental dalam satu transaksi harus menggunakan jadwal pengembalian yang sama.'
                    );
                }
            }
        }

        DB::beginTransaction();

        try {
            $requestedQuantities = [];

            foreach ($cart as $cartItem) {
                $itemId =
                    (int) $cartItem['id'];

                if (
                    !isset(
                        $requestedQuantities[$itemId]
                    )
                ) {
                    $requestedQuantities[$itemId] =
                        0;
                }

                $requestedQuantities[$itemId] +=
                    (int) $cartItem['quantity'];
            }

            $lockedItems = [];

            foreach (
                $requestedQuantities
                as $itemId => $requestedQuantity
            ) {
                $dbItem =
                    Item::lockForUpdate()
                        ->find($itemId);

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

                $lockedItems[$itemId] =
                    $dbItem;

                $sample = null;

                foreach ($cart as $cartItem) {
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
                                $sample['start_date'],
                                $sample['start_time'],
                                $sample['end_date'],
                                $sample['end_time']
                            );

                    if (
                        $overlappingQty +
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
                     * TIDAK decrement physical stock.
                     */
                }

                /*
                |--------------------------------------------------------------------------
                | NON-RETURNABLE
                |--------------------------------------------------------------------------
                */

                else {
                    if (
                        $requestedQuantity
                        >
                        (int) $dbItem->stock_quantity
                    ) {
                        throw new \Exception(
                            "Stok '{$dbItem->name}' tidak mencukupi. Stok tersedia: {$dbItem->stock_quantity} unit."
                        );
                    }

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
                        $firstItem['start_date'],

                    'start_time' =>
                        $firstItem['start_time'],

                    'end_date' =>
                        $firstItem['end_date'] ?? null,

                    'end_time' =>
                        $firstItem['end_time'] ?? null,

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

                $sizeExtra =
                    (int) (
                        $cartItem[
                            'size_additional_price'
                        ]
                        ?? 0
                    );

                $unitPrice +=
                    $sizeExtra;

                /*
                |--------------------------------------------------------------------------
                | STUDENT COUNCIL FREE HABIS PAKAI
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

                $quantity =
                    (int) $cartItem['quantity'];

                $subtotal =
                    $unitPrice *
                    $quantity;

                OrderItem::create([
                    'order_id' =>
                        $order->id,

                    'item_id' =>
                        $cartItem['id'],

                    'quantity' =>
                        $quantity,

                    'size' =>
                        $cartItem['size']
                        ?? null,

                    'design_link' =>
                        $cartItem[
                            'design_link'
                        ]
                        ?? null,

                    'size_additional_price' =>
                        $sizeExtra,

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
                    $mouTypes[$mouType] =
                        true;
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

            session()->forget('cart');

            $this->notifyAdmins(
                "Transaksi baru masuk: {$order->order_number}"
            );

            return redirect()
                ->route('student.loans')
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
                $item['requires_mou']
                ?? false,
                FILTER_VALIDATE_BOOLEAN
            );

        if (!$requiresMou) {
            return null;
        }

        $transactionType =
            $item['transaction_type']
            ?? null;

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
                $item['transaction_detail']
                ?? null
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
                $item['subcategory']
                ?? null
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
            '2XL' => 5000,
            '3XL' => 10000,
            '4XL' => 15000,
            '5XL' => 20000,
            default => 0,
        };
    }

    private function buildLineKey(
        Item $item,
        ?string $size,
        ?string $designLink = null
    ): string {
        $designKey = 'none';

        if ($designLink) {
            $designKey =
                md5(
                    trim($designLink)
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
                === $itemId
            ) {
                $total +=
                    (int) (
                        $cartItem['quantity']
                        ?? 0
                    );
            }
        }

        return $total;
    }

    private function notifyAdmins(
        string $message
    ): void {
        $admins = User::where(
            'role',
            'admin'
        )->get();

        foreach ($admins as $admin) {
            $admin->notify(
                new AdminNotification(
                    $message
                )
            );
        }
    }
}