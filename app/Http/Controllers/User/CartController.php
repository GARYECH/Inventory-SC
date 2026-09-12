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
        $cart = session()->get('cart', []);

        return view('user.cart', compact('cart'));
    }

    public function addToCart(Request $request, Item $item)
    {
        $cart = session()->get('cart', []);

        $requestQuantity = (int) ($request->quantity ?? 1);

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
         * Barang dalam satu transaksi harus berasal
         * dari kelompok transaksi yang sama.
         *
         * Contoh:
         * Baju + ID Card       = boleh
         * HT UV-82 + HT 888s   = boleh
         * ATK + Obat           = boleh
         * HT + Baju             = tidak
         */
        if (!empty($cart)) {
            $firstItem = reset($cart);

            $firstGroup = $this->getOrderGroup(
                $firstItem['transaction_type']
            );

            if ($firstGroup !== $group) {
                return back()->with(
                    'error',
                    'Barang ini tidak bisa dicampur dalam transaksi yang sama. Silakan buat transaksi baru untuk kategori berbeda.'
                );
            }
        }

        $isRental =
            $this->requiresReturn(
                $item->transaction_type
            );

        /*
         * Merchandise information
         */
        $size = null;
        $designLink = null;
        $sizeAdditionalPrice = 0;

        if (
            $item->transaction_type === 'Merchandise'
        ) {
            $subcategory = $item->subcategory;

            if (
                !in_array(
                    $subcategory,
                    [
                        'Baju',
                        'ID Card',
                        'Lainnya'
                    ]
                )
            ) {
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
                    'size' => 'required|in:S,M,L,XL,2XL,3XL,4XL,5XL',
                    'design_link' => 'required|url',
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
                    'design_link' => 'required|url',
                ]);

                $designLink = $request->design_link;
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
                    'min:1'
                ],

                'start_date' => [
                    'required',
                    'date',
                    'after_or_equal:today'
                ],

                'start_time' => [
                    'required',
                    'date_format:H:i'
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

        if (
            !$this->isValidTime(
                $request->start_time
            )
        ) {
            return back()->with(
                'error',
                'Jam transaksi hanya boleh antara 17:00 sampai 19:00.'
            );
        }

        $startDate = $request->start_date;
        $startTime = $request->start_time;

        $endDate = null;
        $endTime = null;

        /*
         * Rental
         */
        if ($isRental) {

            $request->validate([
                'end_date' => [
                    'required',
                    'date',
                    'after_or_equal:start_date'
                ],

                'end_time' => [
                    'required',
                    'date_format:H:i'
                ],
            ]);

            if (
                !$this->isValidTime(
                    $request->end_time
                )
            ) {
                return back()->with(
                    'error',
                    'Jam pengembalian hanya boleh antara 17:00 sampai 19:00.'
                );
            }

            $endDate = $request->end_date;
            $endTime = $request->end_time;

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

            $overlappingQty =
                $this->getOverlappingQuantity(
                    $item->id,
                    $startDate,
                    $startTime,
                    $endDate,
                    $endTime
                );
        } else {
            $overlappingQty = 0;
        }

        /*
         * Cart line key
         *
         * Baju M dan Baju 2XL
         * tidak boleh otomatis tergabung.
         */
        $lineKey = $this->buildLineKey(
            $item,
            $size
        );

        $existingQuantity =
            $cart[$lineKey]['quantity'] ?? 0;

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
         * Save cart
         */
        if (isset($cart[$lineKey])) {

            $cart[$lineKey]['quantity'] +=
                $requestQuantity;

            $cart[$lineKey]['start_date'] =
                $startDate;

            $cart[$lineKey]['start_time'] =
                $startTime;

            $cart[$lineKey]['end_date'] =
                $endDate;

            $cart[$lineKey]['end_time'] =
                $endTime;

            $cart[$lineKey]['size'] =
                $size;

            $cart[$lineKey]['design_link'] =
                $designLink;

            $cart[$lineKey]['size_additional_price'] =
                $sizeAdditionalPrice;

        } else {

            $cart[$lineKey] = [

                'id' =>
                    $item->id,

                'name' =>
                    $item->name,

                'price' =>
                    $item->price,

                'quantity' =>
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
        }

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
                'min:1'
            ]
        ]);

        $itemId =
            $cart[$lineKey]['id'];

        $item =
            Item::findOrFail(
                $itemId
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
                    "Sisa stok '{$item->name}' tidak mencukupi."
                );
            }

        } else {

            if (
                $quantity >
                $item->stock_quantity
            ) {
                return back()->with(
                    'error',
                    "Sisa stok '{$item->name}' tidak mencukupi."
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
         * Validate cart group
         */
        foreach ($cart as $cartItem) {

            if (
                $this->getOrderGroup(
                    $cartItem['transaction_type']
                ) !== $orderType
            ) {
                return back()->with(
                    'error',
                    'Kategori barang dalam keranjang tidak konsisten.'
                );
            }
        }

        $totalPrice = 0;

        foreach (
            $cart as $cartItem
        ) {

            $basePrice =
                (int) $cartItem['price'];

            $additionalPrice =
                (int) (
                    $cartItem['size_additional_price']
                    ?? 0
                );

            $unitPrice =
                $basePrice +
                $additionalPrice;

            /*
             * Student Council FREE
             * for consumables
             */
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
             * Recheck stock
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

            /*
             * Order schedule
             */
            $startDate =
                $firstItem['start_date'];

            $startTime =
                $firstItem['start_time'];

            $endDate =
                $firstItem['end_date']
                ?? null;

            $endTime =
                $firstItem['end_time']
                ?? null;

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
                        $startDate,

                    'start_time' =>
                        $startTime,

                    'end_date' =>
                        $endDate,

                    'end_time' =>
                        $endTime,

                    'is_sop_accepted' =>
                        true,

                    'status' =>
                        'Pending',

                ]);

            /*
             * Create order items
             */
            foreach (
                $cart as $cartItem
            ) {

                $basePrice =
                    (int) $cartItem['price'];

                $sizeAdditional =
                    (int) (
                        $cartItem['size_additional_price']
                        ?? 0
                    );

                $unitPrice =
                    $basePrice +
                    $sizeAdditional;

                if (
                    $request->organization ===
                    'Student Council' &&
                    $orderType === 'Habis Pakai'
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
                        $sizeAdditional,

                    'subtotal_price' =>
                        $subtotal,
                ]);
            }

            /*
             * Create MOU documents
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

            /*
             * Status
             */
            if (!empty($mouTypes)) {
                $order->update([
                    'status' =>
                        'Waiting for MoU'
                ]);
            } else {
                $order->update([
                    'status' =>
                        'Waiting for Payment'
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

    private function getOrderGroup(
        string $transactionType
    ): ?string {

        if (
            $transactionType ===
            'Peralatan'
        ) {
            return 'Peralatan';
        }

        if (
            in_array(
                $transactionType,
                [
                    'HT UV-82',
                    'HT 888s',
                    'HT UV-5R'
                ]
            )
        ) {
            return 'Handy Talkie';
        }

        if (
            in_array(
                $transactionType,
                [
                    'ATK',
                    'Obat'
                ]
            )
        ) {
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
                'HT UV-82',
                'HT 888s',
                'HT UV-5R',
                'Internal Rental',
                'Vendor Rental'
            ]
        );
    }

    private function getMouType(
        array $item
    ): ?string {

        $transactionType =
            $item['transaction_type'];

        if (
            $transactionType ===
            'Peralatan'
        ) {
            return 'peralatan';
        }

        if (
            in_array(
                $transactionType,
                [
                    'HT UV-82',
                    'HT 888s',
                    'HT UV-5R'
                ]
            )
        ) {
            return 'ht';
        }

        if (
            $transactionType ===
            'Internal Rental'
        ) {
            return 'internal';
        }

        if (
            $transactionType ===
            'Vendor Rental'
        ) {
            return 'vendor';
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

    private function getSizeAdditionalPrice(
        string $size
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

        if (
            $item->transaction_type ===
            'Merchandise' &&
            $item->subcategory ===
            'Baju'
        ) {
            return $item->id . '_' . $size;
        }

        return (string) $item->id;
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

    private function getOverlappingQuantity(
        int $itemId,
        string $startDate,
        string $startTime,
        string $endDate,
        string $endTime
    ): int {

        $newStart =
            Carbon::parse(
                "{$startDate} {$startTime}"
            );

        $newEnd =
            Carbon::parse(
                "{$endDate} {$endTime}"
            );

        $orderItems =
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
                            'Cancelled'
                        ]
                    );

                }
            )
            ->with('order')
            ->get();

        $quantity = 0;

        foreach (
            $orderItems as $orderItem
        ) {

            if (
                !$orderItem->order ||
                !$orderItem->order->start_date ||
                !$orderItem->order->start_time ||
                !$orderItem->order->end_date ||
                !$orderItem->order->end_time
            ) {
                continue;
            }

            $existingStart =
                Carbon::parse(
                    $orderItem->order->start_date
                    . ' ' .
                    $orderItem->order->start_time
                );

            $existingEnd =
                Carbon::parse(
                    $orderItem->order->end_date
                    . ' ' .
                    $orderItem->order->end_time
                );

            if (
                $newStart < $existingEnd &&
                $newEnd > $existingStart
            ) {
                $quantity +=
                    $orderItem->quantity;
            }
        }

        return $quantity;
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