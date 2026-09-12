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
    private const TIME_OPTIONS = [
        '17:00',
        '17:30',
        '18:00',
        '18:30',
        '19:00',
    ];

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

        $group = $this->getOrderGroup(
            $item->transaction_type
        );

        if (!$group) {
            return back()->with(
                'error',
                'Jenis transaksi barang tidak valid.'
            );
        }

        /*
         * Satu cart = satu kelompok transaksi.
         *
         * Baju + ID Card = boleh
         * HT UV-82 + HT 888s = boleh
         * ATK + Obat = boleh
         *
         * HT + Baju = tidak
         */
        if (!empty($cart)) {
            $firstItem = reset($cart);

            $firstGroup = $this->getOrderGroup(
                $firstItem['transaction_type']
            );

            if ($firstGroup !== $group) {
                return back()->with(
                    'error',
                    'Barang dari kategori transaksi berbeda tidak dapat digabung dalam satu transaksi.'
                );
            }
        }

        $isRental = $this->requiresReturn(
            $item->transaction_type
        );

        /*
         * Merchandise
         */
        $size = null;
        $designLink = null;
        $sizeAdditionalPrice = 0;

        if (
            $item->transaction_type ===
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
                ]
            )) {
                return back()->with(
                    'error',
                    'Subkategori Merchandise belum ditentukan.'
                );
            }

            /*
             * Baju
             */
            if ($subcategory === 'Baju') {
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

                $size = $request->size;
                $designLink = $request->design_link;

                $sizeAdditionalPrice =
                    $this->getSizeAdditionalPrice(
                        $size
                    );
            }

            /*
             * ID Card
             */
            elseif ($subcategory === 'ID Card') {
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
         * Common validation
         */
        $request->validate(
            [
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
                ],
            ],
            [
                'quantity.required' =>
                    'Jumlah barang wajib diisi.',

                'start_date.required' =>
                    'Tanggal transaksi wajib dipilih.',

                'start_date.after_or_equal' =>
                    'Tanggal transaksi tidak boleh sebelum hari ini.',

                'start_time.required' =>
                    'Jam transaksi wajib dipilih.',
            ]
        );

        if (!$this->isValidTime(
            $request->start_time
        )) {
            return back()->with(
                'error',
                'Jam transaksi hanya boleh antara 17:00 sampai 19:00.'
            );
        }

        $startDate =
            $request->start_date;

        $startTime =
            $request->start_time;

        $endDate = null;
        $endTime = null;

        /*
         * RENTAL
         */
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
                ],
            ]);

            if (!$this->isValidTime(
                $request->end_time
            )) {
                return back()->with(
                    'error',
                    'Jam pengembalian hanya boleh antara 17:00 sampai 19:00.'
                );
            }

            $endDate =
                $request->end_date;

            $endTime =
                $request->end_time;

            $startDateTime = Carbon::parse(
                "{$startDate} {$startTime}"
            );

            $endDateTime = Carbon::parse(
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
         * IMPORTANT:
         * semua item dalam satu order
         * harus menggunakan jadwal yang sama.
         */
        if (!empty($cart)) {
            $existing = reset($cart);

            if (
                ($existing['start_date'] ?? null) !==
                    $startDate ||
                ($existing['start_time'] ?? null) !==
                    $startTime
            ) {
                return back()->with(
                    'error',
                    'Semua barang dalam satu transaksi harus memiliki tanggal dan jam transaksi yang sama.'
                );
            }

            if ($isRental) {
                if (
                    ($existing['end_date'] ?? null) !==
                        $endDate ||
                    ($existing['end_time'] ?? null) !==
                        $endTime
                ) {
                    return back()->with(
                        'error',
                        'Semua barang dalam satu transaksi harus memiliki jadwal pengembalian yang sama.'
                    );
                }
            }
        }

        /*
         * Check rental overlap.
         */
        $overlappingQty = 0;

        if ($isRental) {
            $overlappingQty =
                $this->getOverlappingQuantity(
                    $item->id,
                    $startDate,
                    $startTime,
                    $endDate,
                    $endTime
                );
        }

        /*
         * Cart key.
         */
        $lineKey =
            $this->buildLineKey(
                $item,
                $size
            );

        $existingQuantity =
            (int) (
                $cart[$lineKey]['quantity'] ?? 0
            );

        if ($isRental) {
            $totalRequested =
                $overlappingQty +
                $existingQuantity +
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
         * Save cart.
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
        $cart = session()->get(
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

        $item = Item::findOrFail(
            $cart[$lineKey]['id']
        );

        $quantity =
            (int) $request->quantity;

        $isRental =
            $this->requiresReturn(
                $item->transaction_type
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
        $cart = session()->get(
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

        $firstItem =
            reset($cart);

        $orderType =
            $this->getOrderGroup(
                $firstItem['transaction_type']
            );

        /*
         * Ensure one group.
         */
        foreach (
            $cart as $cartItem
        ) {
            if (
                $this->getOrderGroup(
                    $cartItem['transaction_type']
                ) !== $orderType
            ) {
                return back()->with(
                    'error',
                    'Kategori transaksi dalam keranjang tidak konsisten.'
                );
            }
        }

        /*
         * Ensure one schedule.
         */
        foreach (
            $cart as $cartItem
        ) {
            if (
                ($cartItem['start_date'] ?? null) !==
                    ($firstItem['start_date'] ?? null) ||
                ($cartItem['start_time'] ?? null) !==
                    ($firstItem['start_time'] ?? null)
            ) {
                return back()->with(
                    'error',
                    'Semua barang dalam satu transaksi harus menggunakan jadwal transaksi yang sama.'
                );
            }

            $cartRental =
                $this->requiresReturn(
                    $cartItem['transaction_type']
                );

            if ($cartRental) {
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
         * Calculate total.
         */
        $totalPrice = 0;

        foreach (
            $cart as $cartItem
        ) {
            $unitPrice =
                (int) $cartItem['price'] +
                (int) (
                    $cartItem[
                        'size_additional_price'
                    ] ?? 0
                );

            if (
                $request->organization ===
                'Student Council' &&
                $this->getOrderGroup(
                    $cartItem['transaction_type']
                ) === 'Habis Pakai'
            ) {
                $unitPrice = 0;
            }

            $totalPrice +=
                $unitPrice *
                $cartItem['quantity'];
        }

        DB::beginTransaction();

        try {
            /*
             * Recheck stock.
             */
            foreach (
                $cart as $cartItem
            ) {
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

                $requiresReturn =
                    $this->requiresReturn(
                        $cartItem['transaction_type']
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

                    $dbItem->decrement(
                        'stock_quantity',
                        $cartItem['quantity']
                    );
                }
            }

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
                        $firstItem['end_date']
                        ?? null,

                    'end_time' =>
                        $firstItem['end_time']
                        ?? null,

                    'is_sop_accepted' =>
                        true,

                    'status' =>
                        'Pending',
                ]);

            /*
             * Save order items.
             */
            foreach (
                $cart as $cartItem
            ) {
                $unitPrice =
                    (int) $cartItem['price'] +
                    (int) (
                        $cartItem[
                            'size_additional_price'
                        ] ?? 0
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
                    $cartItem['quantity'];

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
             * Build MOU list.
             *
             * HT variants -> 1 HT MOU.
             * Merchandise:
             * Baju -> Baju MOU.
             * ID Card -> ID Card MOU.
             */
            $mouTypes = [];

            foreach (
                $cart as $cartItem
            ) {
                $mouType =
                    $this->getMouType(
                        $cartItem
                    );

                if (
                    $mouType &&
                    !in_array(
                        $mouType,
                        $mouTypes
                    )
                ) {
                    $mouTypes[] =
                        $mouType;
                }
            }

            foreach (
                $mouTypes as $mouType
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

            session()->forget(
                'cart'
            );

            /*
             * Email masuk queue.
             * User tidak menunggu Gmail.
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

    private function getOrderGroup(
        string $transactionType
    ): ?string {
        if (in_array(
            $transactionType,
            [
                'Peralatan',
                'Internal Rental',
                'Vendor Rental',
            ]
        )) {
            return 'Peralatan';
        }

        if (in_array(
            $transactionType,
            [
                'HT UV-82',
                'HT 888s',
                'HT UV-5R',
            ]
        )) {
            return 'Handy Talkie';
        }

        if (in_array(
            $transactionType,
            [
                'ATK',
                'Obat',
            ]
        )) {
            return 'Habis Pakai';
        }

        if (
            $transactionType ===
            'Merchandise'
        ) {
            return 'Merchandise';
        }

        return null;
    }

    private function requiresReturn(
        string $transactionType
    ): bool {
        return in_array(
            $transactionType,
            [
                'Peralatan',
                'Internal Rental',
                'Vendor Rental',
                'HT UV-82',
                'HT 888s',
                'HT UV-5R',
            ]
        );
    }

    private function getMouType(
        array $item
    ): ?string {
        $transactionType =
            $item['transaction_type'];

        if (
            in_array(
                $transactionType,
                [
                    'Peralatan',
                    'Internal Rental',
                    'Vendor Rental',
                ]
            )
        ) {
            return 'peralatan';
        }

        if (
            in_array(
                $transactionType,
                [
                    'HT UV-82',
                    'HT 888s',
                    'HT UV-5R',
                ]
            )
        ) {
            return 'ht';
        }

        if (
            $transactionType ===
            'Merchandise'
        ) {
            if (
                ($item['subcategory'] ?? null) ===
                'Baju'
            ) {
                return 'baju';
            }

            if (
                ($item['subcategory'] ?? null) ===
                'ID Card'
            ) {
                return 'id_card';
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
            '2XL' => 5000,
            '3XL' => 10000,
            '4XL' => 15000,
            '5XL' => 20000,
            default => 0,
        };
    }

    private function buildLineKey(
        Item $item,
        ?string $size
    ): string {
        return implode(
            '_',
            [
                $item->id,
                $item->subcategory ?? 'none',
                $size ?? 'none',
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
            $orders as $orderItem
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
             * Legacy order without time:
             * treat as all-day conflict.
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
                    $requestedStart->lessThanOrEqualTo(
                        $legacyEnd
                    ) &&
                    $requestedEnd->greaterThanOrEqualTo(
                        $legacyStart
                    )
                ) {
                    $total +=
                        $orderItem->quantity;
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

            /*
             * Time interval overlap.
             */
            if (
                $requestedStart->lt(
                    $existingEnd
                ) &&
                $requestedEnd->gt(
                    $existingStart
                )
            ) {
                $total +=
                    $orderItem->quantity;
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