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
        return view('user.cart', compact('cart')); // (Kita buat view-nya nanti)
    }

    public function addToCart(Request $request, Item $item)
    {
        $cart = session()->get('cart', []);

        // 🚨 LOGIKA SEGREGASI (Pemisah Jalur) 🚨
        // Jika keranjang tidak kosong, cek apakah tipe transaksinya sama!
        if (count($cart) > 0) {
            $firstItem = reset($cart);
            if ($firstItem['transaction_type'] !== $item->transaction_type) {
                return back()->with('error', "WOY! Kamu nggak bisa mencampur barang '{$item->transaction_type}' dengan '{$firstItem['transaction_type']}' di satu kuitansi. Checkout dulu yang ada di keranjang!");
            }
        }

        // Jika lolos, masukkan ke keranjang session
        $cart[$item->id] = [
            'id' => $item->id,
            'name' => $item->name,
            'price' => $item->price,
            'quantity' => $request->quantity ?? 1,
            'transaction_type' => $item->transaction_type,
            'requires_mou' => $item->requires_mou,
        ];

        session()->put('cart', $cart);

        return back()->with('success', 'Barang berhasil masuk keranjang!');
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

        // Validasi form dari mahasiswa (termasuk centang SOP)
        $request->validate([
            'phone_number' => 'required|string',
            'proker_name' => 'required|string',
            'department' => 'required|string',
            'treasurer_name' => 'required|string',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_sop_accepted' => 'required|accepted', // 🚨 INI WAJIB DICENTANG!
        ], [
            'is_sop_accepted.accepted' => 'Kamu HARUS mencentang persetujuan SOP/Terms & Conditions sebelum checkout!'
        ]);

        // Ambil tipe transaksi dari barang pertama di keranjang
        $firstItem = reset($cart);
        $orderType = $firstItem['transaction_type'];

        // Gunakan DB Transaction agar jika gagal di tengah jalan, data tidak acak-acakan
        DB::beginTransaction();

        try {
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
                
                // 🚦 INI DIA! Langsung masuk "Ruang Tunggu Admin" sesuai mintamu
                'status' => Order::STATUS_PENDING_REVIEW, 
            ]);

            // 2. Pindahkan isi Session Keranjang ke tabel order_items di Database
            foreach ($cart as $item) {
                // Logika Diskon BEM/Internal (Harga 0) bisa dimasukkan di sini jika mau
                $subtotal = $item['price'] * $item['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'subtotal_price' => $subtotal,
                ]);
            }

            // 3. Bersihkan keranjang
            session()->forget('cart');

            DB::commit();

            // Lempar mahasiswa ke halaman detail pesanan mereka
            return redirect()->route('user.orders.index')->with('success', 'Checkout berhasil! Kuitansimu sedang menunggu persetujuan (Approval) dari Admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}