<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        $requestQuantity = $request->quantity ?? 1;

        // Validasi: Jangan biarkan order melebihi total aset SC
        if ($item->stock_quantity < $requestQuantity) {
            return back()->with('error', 'Gagal! Stok total aset tidak mencukupi.');
        }

        if (count($cart) > 0) {
            $firstItem = reset($cart);
            if ($firstItem['transaction_type'] !== $item->transaction_type) {
                return back()->with('error', "WOY! Kamu nggak bisa mencampur barang '{$item->transaction_type}' dengan '{$firstItem['transaction_type']}'. Checkout dulu yang ada di keranjang!");
            }
        }

        if (isset($cart[$item->id])) {
            $newQuantity = $cart[$item->id]['quantity'] + $requestQuantity;
            if ($item->stock_quantity < $newQuantity) {
                return back()->with('error', 'Gagal! Total barang ini di keranjang melebihi total aset SC.');
            }
            $cart[$item->id]['quantity'] = $newQuantity;
        } else {
            $cart[$item->id] = [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => $requestQuantity,
                'transaction_type' => $item->transaction_type,
                'requires_mou' => $item->requires_mou,
            ];
        }

        session()->put('cart', $cart);
        return back()->with('success', 'Barang ditambahkan ke keranjang!');
    }

    public function clearCart()
    {
        session()->forget('cart');
        return back()->with('success', 'Keranjang dikosongkan.');
    }

    public function processCheckout(Request $request)
    {
        $cart = session()->get('cart');

        if (!$cart || count($cart) == 0) {
            return back()->with('error', 'Keranjangmu kosong!');
        }

        $request->validate([
            'full_name' => 'required|string',
            'organization' => 'required|string',
            'position' => 'required|string',
            'phone_number' => 'required|string',
            'proker_name' => 'required|string',
            'treasurer_name' => 'required|string',
            'address' => 'required|string',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_sop_accepted' => 'required|accepted',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $firstItem = reset($cart);
        $orderType = $firstItem['transaction_type'];

        // ==========================================================
        // 🌟 LOGIKA KALENDER: CEK KETERSEDIAAN DI TANGGAL TERSEBUT 🌟
        // ==========================================================
        if ($startDate && $endDate && str_contains($orderType, 'Rental')) {
            foreach ($cart as $id => $details) {
                $item = Item::find($id);

                // Hitung total unit yang sedang dipinjam oleh order lain PADA TANGGAL YANG BERSINGGUNGAN
                $overlappingQty = OrderItem::where('item_id', $id)
                    ->whereHas('order', function ($query) use ($startDate, $endDate) {
                        $query->whereNotIn('status', ['Returned', 'Rejected', 'Cancelled'])
                              ->where('order_type', '!=', 'Sale') // Abaikan transaksi Beli Putus
                              ->where('start_date', '<=', $endDate)  // Rumus Overlap Tgl
                              ->where('end_date', '>=', $startDate); // Rumus Overlap Tgl
                    })->sum('quantity');

                // Kapasitas maksimal SC
                $stokMaksimal = $item->stock_quantity;

                // Jika (yang sedang dipakai orang di tanggal tsb + yang mau kita pinjam) > Kapasitas SC
                if (($overlappingQty + $details['quantity']) > $stokMaksimal) {
                    $sisaKuota = $stokMaksimal - $overlappingQty;
                    $formatStart = Carbon::parse($startDate)->format('d M');
                    $formatEnd = Carbon::parse($endDate)->format('d M Y');
                    return back()->with('error', "Gagal! Barang '{$item->name}' bentrok. Pada {$formatStart} - {$formatEnd}, sisa barang yang tersedia hanya {$sisaKuota} unit.");
                }
            }
        }

        // 🌟 HITUNG GRAND TOTAL + DISKON 100% STUDENT COUNCIL
        $totalPrice = 0;
        foreach ($cart as $item) {
            $dbItem = Item::find($item['id']);
            $unitPrice = $item['price'];
            if ($request->organization === 'Student Council' && str_contains($dbItem->transaction_type, 'Internal')) {
                $unitPrice = 0; // SC Internal = Gratis!
            }
            $totalPrice += ($unitPrice * $item['quantity']);
        }

        DB::beginTransaction();

        try {
            // 🛡️ THE GATEKEEPER: HANYA DECREMENT STOK JIKA BELI PUTUS (SALE)
            foreach ($cart as $item) {
                if ($orderType === 'Sale') {
                    $dbItem = Item::lockForUpdate()->find($item['id']);
                    if (!$dbItem || $dbItem->stock_quantity < $item['quantity']) {
                        DB::rollBack();
                        return back()->with('error', "Gagal! Stok '{$item['name']}' habis.");
                    }
                    // Beli putus = barang hilang dari gudang selamanya
                    $dbItem->decrement('stock_quantity', $item['quantity']);
                }
                // JIKA RENTAL: Kita biarkan stoknya UTUH! Kita cuma catat transaksinya.
            }

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'user_id' => auth()->id(),
                'full_name' => $request->full_name,
                'organization' => $request->organization,
                'position' => $request->position,
                'phone_number' => $request->phone_number,
                'proker_name' => $request->proker_name,
                'treasurer_name' => $request->treasurer_name,
                'address' => $request->address,
                'order_type' => $orderType,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_sop_accepted' => true,
                'total_price' => $totalPrice, 
                'status' => 'Pending',
            ]);

            foreach ($cart as $item) {
                $dbItem = Item::find($item['id']);
                $unitPrice = $item['price'];
                if ($request->organization === 'Student Council' && str_contains($dbItem->transaction_type, 'Internal')) {
                    $unitPrice = 0;
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $unitPrice,
                    'subtotal_price' => ($unitPrice * $item['quantity']),
                ]);
            }

            session()->forget('cart');
            DB::commit();

            return redirect()->route('student.loans')->with('success', 'Checkout berhasil! Kuitansimu sedang menunggu persetujuan Admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}