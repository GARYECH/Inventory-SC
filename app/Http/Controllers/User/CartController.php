<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Notifications\AdminNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function viewCart()
    {
        $cart = session()->get('cart', []);

        return view('user.cart', compact('cart'));
    }

    public function addToCart(Request $request, Item $item)
    {
        $cart = session()->get('cart', []);
        $requestQuantity = (int) ($request->quantity ?? 1);

        $requiresReturn = in_array($item->transaction_type, [
            'Peralatan',
            'HT UV-82',
            'HT 888s',
            'HT UV-5R',
            'Internal Rental',
            'Vendor Rental'
        ]);

        if (count($cart) > 0) {
            $firstItem = reset($cart);

            if (
                $firstItem['transaction_type'] !==
                $item->transaction_type
            ) {
                return back()->with(
                    'error',
                    "Kamu tidak bisa mencampur kategori '{$firstItem['transaction_type']}' dengan '{$item->transaction_type}' dalam satu keranjang."
                );
            }

            if (
                $item->transaction_type === 'Merchandise' &&
                ($firstItem['subcategory'] ?? null) !== $item->subcategory
            ) {
                return back()->with(
                    'error',
                    'Kamu tidak bisa mencampur subkategori Merchandise dalam satu keranjang.'
                );
            }
        }

        $rules = [
            'quantity' => 'required|integer|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
        ];

        if ($requiresReturn) {
            $rules['end_date'] = 'required|date|after_or_equal:start_date';
            $rules['end_time'] = 'required|date_format:H:i';
        }

        $request->validate(
            $rules,
            [
                'quantity.required' => 'Jumlah barang wajib diisi.',
                'start_date.required' => 'Pilih tanggal transaksi terlebih dahulu.',
                'start_date.after_or_equal' => 'Tanggal tidak boleh sebelum hari ini.',
                'start_time.required' => 'Pilih jam transaksi terlebih dahulu.',
                'start_time.date_format' => 'Format jam harus HH:MM.',
                'end_date.required' => 'Pilih tanggal pengembalian terlebih dahulu.',
                'end_date.after_or_equal' => 'Tanggal pengembalian tidak boleh sebelum tanggal transaksi.',
                'end_time.required' => 'Pilih jam pengembalian terlebih dahulu.',
                'end_time.date_format' => 'Format jam harus HH:MM.',
            ]
        );

        if (
            !$this->isValidTime($request->start_time)
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

        if ($requiresReturn) {
            if (!$this->isValidTime($request->end_time)) {
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

            if ($endDateTime->lessThanOrEqualTo($startDateTime)) {
                return back()->with(
                    'error',
                    'Waktu pengembalian harus setelah waktu pengambilan.'
                );
            }

            $overlappingQty = $this->getOverlappingQuantity(
                $item->id,
                $startDate,
                $startTime,
                $endDate,
                $endTime
            );

            $qtyInCart = $cart[$item->id]['quantity'] ?? 0;
            $totalRequested = $overlappingQty + $qtyInCart + $requestQuantity;

            if ($totalRequested > $item->stock_quantity) {
                $remainingStock = max(
                    0,
                    $item->stock_quantity -
                    $overlappingQty -
                    $qtyInCart
                );

                return back()->with(
                    'error',
                    "Gagal! Untuk jadwal tersebut, sisa stok '{$item->name}' hanya {$remainingStock} unit."
                );
            }
        } else {
            $qtyInCart = $cart[$item->id]['quantity'] ?? 0;

            if (
                ($qtyInCart + $requestQuantity) >
                $item->stock_quantity
            ) {
                return back()->with(
                    'error',
                    "Gagal! Stok '{$item->name}' hanya {$item->stock_quantity} unit."
                );
            }
        }

        if (isset($cart[$item->id])) {
            $cart[$item->id]['quantity'] += $requestQuantity;
            $cart[$item->id]['start_date'] = $startDate;
            $cart[$item->id]['start_time'] = $startTime;
            $cart[$item->id]['end_date'] = $endDate;
            $cart[$item->id]['end_time'] = $endTime;
        } else {
            $cart[$item->id] = [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => $requestQuantity,
                'transaction_type' => $item->transaction_type,
                'subcategory' => $item->subcategory,
                'requires_mou' => $item->requires_mou,
                'start_date' => $startDate,
                'start_time' => $startTime,
                'end_date' => $endDate,
                'end_time' => $endTime,
            ];
        }

        session()->put('cart', $cart);

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

    public function updateCart(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (!isset($cart[$id])) {
            return back()->with(
                'error',
                'Barang tidak ditemukan di keranjang.'
            );
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item = Item::findOrFail($id);
        $newQty = (int) $request->quantity;

        $requiresReturn = in_array($item->transaction_type, [
            'Peralatan',
            'HT UV-82',
            'HT 888s',
            'HT UV-5R',
            'Internal Rental',
            'Vendor Rental'
        ]);

        $startDate = $cart[$id]['start_date'] ?? null;
        $startTime = $cart[$id]['start_time'] ?? null;
        $endDate = $cart[$id]['end_date'] ?? null;
        $endTime = $cart[$id]['end_time'] ?? null;

        if (!$startDate || !$startTime) {
            return back()->with(
                'error',
                'Jadwal transaksi belum lengkap. Silakan masukkan barang kembali dari katalog.'
            );
        }

        if (!$this->isValidTime($startTime)) {
            return back()->with(
                'error',
                'Jam transaksi hanya boleh antara 17:00 sampai 19:00.'
            );
        }

        if ($requiresReturn) {
            if (!$endDate || !$endTime) {
                return back()->with(
                    'error',
                    'Jadwal pengembalian belum lengkap.'
                );
            }

            if (!$this->isValidTime($endTime)) {
                return back()->with(
                    'error',
                    'Jam pengembalian hanya boleh antara 17:00 sampai 19:00.'
                );
            }

            $overlappingQty = $this->getOverlappingQuantity(
                $item->id,
                $startDate,
                $startTime,
                $endDate,
                $endTime
            );

            if (
                ($overlappingQty + $newQty) >
                $item->stock_quantity
            ) {
                $remainingStock = max(
                    0,
                    $item->stock_quantity - $overlappingQty
                );

                return back()->with(
                    'error',
                    "Gagal update! Sisa stok '{$item->name}' hanya {$remainingStock} unit."
                );
            }
        } elseif ($newQty > $item->stock_quantity) {
            return back()->with(
                'error',
                "Gagal update! Stok gudang hanya {$item->stock_quantity} unit."
            );
        }

        $cart[$id]['quantity'] = $newQty;

        session()->put('cart', $cart);

        return back()->with(
            'success',
            'Jumlah barang berhasil diupdate!'
        );
    }

    public function removeItem($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return back()->with(
            'success',
            'Barang berhasil dihapus dari keranjang.'
        );
    }

    public function processCheckout(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return back()->with(
                'error',
                'Keranjangmu kosong!'
            );
        }

        $request->validate([
            'full_name' => 'required|string',
            'organization' => 'required|string',
            'position' => 'required|string',
            'phone_number' => 'required|string',
            'proker_name' => 'required|string',
            'ketua_acara' => 'required|string',
            'treasurer_name' => 'required|string',
            'address' => 'required|string',
            'notes' => 'nullable|string',
            'is_sop_accepted' => 'required|accepted',
        ]);

        $firstItem = reset($cart);
        $transactionType = $firstItem['transaction_type'];

        $orderType = $this->getOrderType($transactionType);

        $globalStartDate = null;
        $globalStartTime = null;
        $globalEndDate = null;
        $globalEndTime = null;
        $totalPrice = 0;

        foreach ($cart as $id => $item) {
            if (
                empty($item['start_date']) ||
                empty($item['start_time'])
            ) {
                return back()->with(
                    'error',
                    'Setiap transaksi wajib memiliki tanggal dan jam.'
                );
            }

            if (!$this->isValidTime($item['start_time'])) {
                return back()->with(
                    'error',
                    'Jam transaksi hanya boleh antara 17:00 sampai 19:00.'
                );
            }

            if (
                is_null($globalStartDate) ||
                $item['start_date'] < $globalStartDate
            ) {
                $globalStartDate = $item['start_date'];
            }

            if (
                is_null($globalStartTime) ||
                $item['start_time'] < $globalStartTime
            ) {
                $globalStartTime = $item['start_time'];
            }

            if (!empty($item['end_date'])) {
                if (
                    is_null($globalEndDate) ||
                    $item['end_date'] > $globalEndDate
                ) {
                    $globalEndDate = $item['end_date'];
                }

                if (
                    is_null($globalEndTime) ||
                    $item['end_time'] > $globalEndTime
                ) {
                    $globalEndTime = $item['end_time'];
                }
            }

            $dbItem = Item::find($id);

            if (!$dbItem) {
                return back()->with(
                    'error',
                    'Barang tidak ditemukan.'
                );
            }

            $unitPrice = $item['price'];

            if (
                $request->organization === 'Student Council' &&
                in_array($dbItem->transaction_type, [
                    'Peralatan',
                    'HT UV-5R'
                ])
            ) {
                $unitPrice = 0;
            }

            $totalPrice += $unitPrice * $item['quantity'];
        }

        DB::beginTransaction();

        try {
            foreach ($cart as $id => $item) {
                $dbItem = Item::lockForUpdate()->find($id);

                if (!$dbItem) {
                    DB::rollBack();

                    return back()->with(
                        'error',
                        "Barang '{$item['name']}' tidak ditemukan."
                    );
                }

                $requiresReturn = !empty($item['end_date']);

                if ($requiresReturn) {
                    $overlappingQty = $this->getOverlappingQuantity(
                        $id,
                        $item['start_date'],
                        $item['start_time'],
                        $item['end_date'],
                        $item['end_time']
                    );

                    if (
                        ($overlappingQty + $item['quantity']) >
                        $dbItem->stock_quantity
                    ) {
                        DB::rollBack();

                        return back()->with(
                            'error',
                            "Maaf, barang '{$item['name']}' baru saja dibooking orang lain pada jadwal tersebut."
                        );
                    }
                } else {
                    if (
                        $dbItem->stock_quantity <
                        $item['quantity']
                    ) {
                        DB::rollBack();

                        return back()->with(
                            'error',
                            "Gagal! Stok '{$item['name']}' keburu habis."
                        );
                    }

                    $dbItem->decrement(
                        'stock_quantity',
                        $item['quantity']
                    );
                }
            }

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'user_id' => auth()->id(),
                'full_name' => $request->full_name,
                'organization' => $request->organization,
                'position' => $request->position,
                'phone_number' => $request->phone_number,
                'proker_name' => $request->proker_name,
                'ketua_acara' => $request->ketua_acara,
                'department' => '-',
                'treasurer_name' => $request->treasurer_name,
                'address' => $request->address,
                'notes' => $request->notes,
                'order_type' => $orderType,
                'start_date' => $globalStartDate,
                'start_time' => $globalStartTime,
                'end_date' => $globalEndDate,
                'end_time' => $globalEndTime,
                'is_sop_accepted' => true,
                'total_price' => $totalPrice,
                'status' => 'Pending',
            ]);

            foreach ($cart as $item) {
                $dbItem = Item::find($item['id']);
                $unitPrice = $item['price'];

                if (
                    $request->organization === 'Student Council' &&
                    in_array($dbItem->transaction_type, [
                        'Peralatan',
                        'HT UV-5R'
                    ])
                ) {
                    $unitPrice = 0;
                }

                OrderItem::create([
                    'order_number' => $order->id,
                    'order_id' => $order->id,
                    'item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $unitPrice,
                    'subtotal_price' => $unitPrice * $item['quantity'],
                ]);
            }

            session()->forget('cart');

            $admins = User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                $admin->notify(
                    new AdminNotification(
                        'Order Baru: ' .
                        $order->order_number .
                        ' dari ' .
                        $order->proker_name
                    )
                );
            }

            DB::commit();

            return redirect()
                ->route('student.loans')
                ->with(
                    'success',
                    'Checkout berhasil! Pengajuanmu sedang menunggu persetujuan Admin.'
                );
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with(
                'error',
                'Terjadi kesalahan sistem: ' . $e->getMessage()
            );
        }
    }

    private function isValidTime($time)
    {
        try {
            $selectedTime = Carbon::createFromFormat('H:i', $time);
            $minimumTime = Carbon::createFromFormat('H:i', '17:00');
            $maximumTime = Carbon::createFromFormat('H:i', '19:00');

            return $selectedTime->betweenIncluded(
                $minimumTime,
                $maximumTime
            );
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getOrderType($transactionType)
    {
        if ($transactionType === 'Merchandise') {
            return 'Merchandise';
        }

        if (
            in_array($transactionType, [
                'ATK',
                'Obat'
            ])
        ) {
            return 'Habis Pakai';
        }

        if (
            in_array($transactionType, [
                'HT UV-82',
                'HT 888s',
                'HT UV-5R'
            ])
        ) {
            return 'Handy Talkie';
        }

        return 'Peralatan';
    }

    private function getOverlappingQuantity(
        $itemId,
        $startDate,
        $startTime,
        $endDate,
        $endTime
    ) {
        if (
            empty($startDate) ||
            empty($startTime) ||
            empty($endDate) ||
            empty($endTime)
        ) {
            return 0;
        }

        $requestedStart = Carbon::parse(
            "{$startDate} {$startTime}"
        );

        $requestedEnd = Carbon::parse(
            "{$endDate} {$endTime}"
        );

        $orders = OrderItem::where('item_id', $itemId)
            ->whereHas('order', function ($query) use (
                $startDate,
                $endDate
            ) {
                $query
                    ->whereNotIn('status', [
                        'Returned',
                        'Resolved (Fine Paid)',
                        'Rejected',
                        'Cancelled'
                    ])
                    ->where('start_date', '<=', $endDate)
                    ->where('end_date', '>=', $startDate);
            })
            ->with('order')
            ->get();

        $total = 0;

        foreach ($orders as $orderItem) {
            $order = $orderItem->order;

            if (
                !$order ||
                !$order->start_date ||
                !$order->start_time ||
                !$order->end_date ||
                !$order->end_time
            ) {
                continue;
            }

            $existingStart = Carbon::parse(
                "{$order->start_date} {$order->start_time}"
            );

            $existingEnd = Carbon::parse(
                "{$order->end_date} {$order->end_time}"
            );

            if (
                $existingStart->lt($requestedEnd) &&
                $existingEnd->gt($requestedStart)
            ) {
                $total += $orderItem->quantity;
            }
        }

        return $total;
    }
}