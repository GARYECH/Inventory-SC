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

        // Validasi form dari mahasiswa (🌟 TAMBAH ALAMAT DI SINI)
        $request->validate([
            'phone_number' => 'required|string',
            'proker_name' => 'required|string',
            'department' => 'required|string',
            'treasurer_name' => 'required|string',
            'address' => 'required|string', // 🌟 KITA TANGKAP ALAMAT
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_sop_accepted' => 'required|accepted',
        ], [
            'is_sop_accepted.accepted' => 'Kamu HARUS mencentang persetujuan SOP/Terms & Conditions sebelum checkout!'
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // ==========================================================
        // 🌟 THE BRAIN: LOGIKA ANTI-BENTROK (OVERLAPPING DATES) 🌟
        // ==========================================================
        if ($startDate && $endDate) {
            foreach ($cart as $id => $details) {
                $item = Item::find($id);

                // A. Hitung total unit yang SIBUK (dipinjam orang lain) tepat pada rentang tanggal tersebut
                $overlappingQty = OrderItem::where('item_id', $id)
                    ->whereHas('order', function ($query) use ($startDate, $endDate) {
                        $query->whereNotIn('status', ['Returned', 'Rejected', 'Cancelled'])
                              ->where('start_date', '<=', $endDate)  // Rumus Irisan Waktu
                              ->where('end_date', '>=', $startDate); // Rumus Irisan Waktu
                    })->sum('quantity');

                // B. Hitung total stok ASLI yang SC miliki (Stok di gudang + Total yang sedang dipinjam secara keseluruhan)
                $totalSedangDipinjam = OrderItem::where('item_id', $id)
                    ->whereHas('order', function ($q) {
                        $q->whereNotIn('status', ['Returned', 'Rejected', 'Cancelled']);
                    })->sum('quantity');
                
                $stokAsli = $item->stock_quantity + $totalSedangDipinjam;

                // C. EKSEKUSI PEMBLOKIRAN: Jika (Yang bentrok + Yang mau dipinjam > Stok Asli SC), TOLAK!
                if (($overlappingQty + $details['quantity']) > $stokAsli) {
                    $sisaKuota = $stokAsli - $overlappingQty;
                    $formatStart = \Carbon\Carbon::parse($startDate)->format('d M');
                    $formatEnd = \Carbon\Carbon::parse($endDate)->format('d M Y');
                    
                    return back()->with('error', "Gagal Checkout! Barang '{$item->name}' jadwalnya bentrok. Pada tanggal {$formatStart} s/d {$formatEnd} sisa kuota hanya {$sisaKuota} unit. Silakan cek kalender merah di katalog.");
                }
            }
        }
        // ==========================================================

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
                $dbItem = Item::lockForUpdate()->find($item['id']);
                
                if (!$dbItem || $dbItem->stock_quantity < $item['quantity']) {
                    DB::rollBack();
                    return back()->with('error', "Gagal! Stok '{$item['name']}' tiba-tiba habis dipinjam orang lain saat kamu mau bayar.");
                }
                
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
                'address' => $request->address, // 🌟 MASUKKAN ALAMAT KE DATABASE
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