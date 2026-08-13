<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // ==========================================================
    // 🛒 FITUR KERANJANG
    // ==========================================================
    
    public function viewCart()
    {
        $cart = session()->get('cart', []);
        return view('user.cart', compact('cart'));
    }

    public function addToCart(Request $request, Item $item)
    {
        $cart = session()->get('cart', []);
        $requestQuantity = $request->quantity ?? 1;

        // 🛡️ VALIDASI 1: Cek apakah stok di gudang cukup
        if ($item->stock_quantity < $requestQuantity) {
            return back()->with('error', 'Gagal! Stok barang tidak mencukupi.');
        }

        // 🚨 LOGIKA SEGREGASI (Pemisah Jalur)
        if (count($cart) > 0) {
            $firstItem = reset($cart);
            if ($firstItem['transaction_type'] !== $item->transaction_type) {
                return back()->with('error', "WOY! Kamu nggak bisa mencampur barang '{$item->transaction_type}' dengan '{$firstItem['transaction_type']}' di satu kuitansi. Checkout dulu yang ada di keranjang!");
            }
        }

        // 🛡️ VALIDASI 2: Akumulasi Qty & Cek Stok Gabungan
        if (isset($cart[$item->id])) {
            $newQuantity = $cart[$item->id]['quantity'] + $requestQuantity;
            
            if ($item->stock_quantity < $newQuantity) {
                return back()->with('error', 'Gagal! Total barang ini di keranjangmu melebihi sisa stok di gudang.');
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

        return back()->with('success', 'Barang berhasil ditambahkan ke keranjang!');
    }

    public function clearCart()
    {
        session()->forget('cart');
        return back()->with('success', 'Keranjang dikosongkan.');
    }

    // ==========================================================
    // 🚀 FITUR CHECKOUT (SOP & PEMBUATAN ORDER)
    // ==========================================================

    public function processCheckout(Request $request)
    {
        $cart = session()->get('cart');

        if (!$cart || count($cart) == 0) {
            return back()->with('error', 'Keranjangmu kosong!');
        }

        // Validasi form dari mahasiswa
        $request->validate([
            'phone_number' => 'required|string',
            'proker_name' => 'required|string',
            'department' => 'required|string',
            'treasurer_name' => 'required|string',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_sop_accepted' => 'required|accepted',
        ], [
            'is_sop_accepted.accepted' => 'Kamu HARUS mencentang persetujuan SOP/Terms & Conditions sebelum checkout!'
        ]);

        $firstItem = reset($cart);
        $orderType = $firstItem['transaction_type'];

        $totalPrice = 0;
        foreach ($cart as $item) {
            $totalPrice += $item['price'] * $item['quantity'];
        }

        DB::beginTransaction();

        try {
            // 🛡️ THE GATEKEEPER: Validasi Ulang & Pemotongan Stok Pakai Lock!
            foreach ($cart as $item) {
                // lockForUpdate() akan mengunci baris data ini dari request lain sampai transaksi ini beres
                $dbItem = Item::lockForUpdate()->find($item['id']);
                
                if (!$dbItem || $dbItem->stock_quantity < $item['quantity']) {
                    DB::rollBack();
                    return back()->with('error', "Gagal! Stok '{$item['name']}' tiba-tiba habis dipinjam orang lain saat kamu mau bayar.");
                }
                
                // Potong stoknya langsung di database!
                $dbItem->decrement('stock_quantity', $item['quantity']);
            }

            // 1. Buat Kuitansi Induk (Order)
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'user_id' => auth()->id(),
                'phone_number' => $request->phone_number,
                'proker_name' => $request->proker_name,
                'department' => $request->department,
                'treasurer_name' => $request->treasurer_name,
                'order_type' => $orderType,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_sop_accepted' => true,
                'total_price' => $totalPrice, 
                'status' => 'Pending',
            ]);

            // 2. Pindahkan isi Session Keranjang ke tabel order_items
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'subtotal_price' => ($item['price'] * $item['quantity']),
                ]);
            }

            // 3. Bersihkan keranjang
            session()->forget('cart');

            DB::commit();

            return redirect()->route('student.loans')->with('success', 'Checkout berhasil! Kuitansimu sedang menunggu persetujuan (Approval) dari Admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}