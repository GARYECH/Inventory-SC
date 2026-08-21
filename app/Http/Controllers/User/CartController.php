<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Notifications\AdminNotification;
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

    // ==========================================================
    // 🌟 FASE 1: ADD TO CART DENGAN SISTEM TRAVELOKA (CEK JADWAL DI AWAL) 🌟
    // ==========================================================
    public function addToCart(Request $request, Item $item)
    {
        $cart = session()->get('cart', []);
        $requestQuantity = $request->quantity ?? 1;
        $isRental = str_contains($item->transaction_type, 'Rental');

        // 1. CEK CAMPUR BARANG
        if (count($cart) > 0) {
            $firstItem = reset($cart);
            if ($firstItem['transaction_type'] !== $item->transaction_type) {
                return back()->with('error', "WOY! Kamu nggak bisa mencampur barang '{$item->transaction_type}' dengan '{$firstItem['transaction_type']}'. Checkout dulu yang ada di keranjang!");
            }
        }

        // 2. LOGIKA PENGECEKAN STOK & JADWAL (LANGSUNG DARI KATALOG)
        if ($isRental) {
            // Wajib kirim tanggal dari form add to cart (katalog)
            $request->validate([
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
            ], [
                'start_date.required' => 'Pilih tanggal mulai sewa terlebih dahulu!',
                'end_date.required' => 'Pilih tanggal selesai sewa terlebih dahulu!',
            ]);

            $startDate = $request->start_date;
            $endDate = $request->end_date;

            // Hitung barang yang overlap di tanggal tersebut
            $overlappingQty = OrderItem::where('item_id', $item->id)
                ->whereHas('order', function ($query) use ($startDate, $endDate) {
                    $query->whereNotIn('status', ['Returned', 'Resolved (Fine Paid)', 'Rejected', 'Cancelled'])
                          ->where('order_type', '!=', 'Sale') 
                          ->where('start_date', '<=', $endDate) 
                          ->where('end_date', '>=', $startDate); 
                })->sum('quantity');

            // Hitung barang INI yang sudah masuk keranjang di sesi yang sama
            $qtyInCart = isset($cart[$item->id]) ? $cart[$item->id]['quantity'] : 0;
            $totalDiminta = $overlappingQty + $qtyInCart + $requestQuantity;

            // Jika melebihi kapasitas gudang di tanggal tsb, TOLAK!
            if ($totalDiminta > $item->stock_quantity) {
                $sisaKuota = max(0, $item->stock_quantity - $overlappingQty - $qtyInCart);
                $formatStart = Carbon::parse($startDate)->format('d M');
                $formatEnd = Carbon::parse($endDate)->format('d M');
                return back()->with('error', "Gagal! Untuk tanggal {$formatStart} - {$formatEnd}, sisa stok '{$item->name}' hanya {$sisaKuota} unit.");
            }
        } else {
            // Logika untuk Sale (Merchandise Beli Putus) tanpa tanggal
            $qtyInCart = isset($cart[$item->id]) ? $cart[$item->id]['quantity'] : 0;
            if (($qtyInCart + $requestQuantity) > $item->stock_quantity) {
                return back()->with('error', 'Gagal! Stok total aset tidak mencukupi.');
            }
            $startDate = null;
            $endDate = null;
        }

        // 3. MASUKKAN KE KERANJANG BESERTA JADWALNYA
        if (isset($cart[$item->id])) {
            $cart[$item->id]['quantity'] += $requestQuantity;
            // Timpa tanggal lama dengan tanggal baru jika diupdate
            $cart[$item->id]['start_date'] = $startDate;
            $cart[$item->id]['end_date'] = $endDate;
        } else {
            $cart[$item->id] = [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => $requestQuantity,
                'transaction_type' => $item->transaction_type,
                'requires_mou' => $item->requires_mou,
                'start_date' => $startDate, // 🌟 SIMPAN TANGGAL DI KERANJANG
                'end_date' => $endDate,     // 🌟 SIMPAN TANGGAL DI KERANJANG
            ];
        }

        session()->put('cart', $cart);
        return back()->with('success', 'Barang berhasil masuk keranjang!');
    }

    public function clearCart()
    {
        session()->forget('cart');
        return back()->with('success', 'Keranjang dikosongkan.');
    }
    // 🌟 FITUR UPDATE QTY DI CHECKOUT (DENGAN CEK OVERLAP JADWAL) 🌟
    public function updateCart(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$id])) {
            $item = Item::findOrFail($id);
            $newQty = $request->quantity;
            $isRental = str_contains($item->transaction_type, 'Rental');

            if ($isRental) {
                $startDate = $cart[$id]['start_date'];
                $endDate = $cart[$id]['end_date'];

                // Hitung barang yang overlap (tidak termasuk punya dia sendiri di keranjang saat ini)
                $overlappingQty = OrderItem::where('item_id', $item->id)
                    ->whereHas('order', function ($query) use ($startDate, $endDate) {
                        $query->whereNotIn('status', ['Returned', 'Resolved (Fine Paid)', 'Rejected', 'Cancelled'])
                              ->where('order_type', '!=', 'Sale') 
                              ->where('start_date', '<=', $endDate) 
                              ->where('end_date', '>=', $startDate); 
                    })->sum('quantity');

                if (($overlappingQty + $newQty) > $item->stock_quantity) {
                    $sisaKuota = max(0, $item->stock_quantity - $overlappingQty);
                    return back()->with('error', "Gagal update! Sisa stok '{$item->name}' di rentang tanggal tersebut hanya {$sisaKuota} unit.");
                }
            } else {
                if ($newQty > $item->stock_quantity) {
                    return back()->with('error', "Gagal update! Sisa stok gudang hanya {$item->stock_quantity} unit.");
                }
            }

            // Lolos Cek! Update jumlahnya
            $cart[$id]['quantity'] = $newQty;
            session()->put('cart', $cart);
            return back()->with('success', 'Jumlah barang berhasil diupdate!');
        }

        return back()->with('error', 'Barang tidak ditemukan di keranjang.');
    }

    // 🌟 FITUR HAPUS 1 BARANG DARI KERANJANG 🌟
    public function removeItem($id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return back()->with('success', 'Barang berhasil dihapus dari keranjang.');
    }

    // ==========================================================
    // 🌟 FASE 2: CHECKOUT (FINALISASI DATA MAHASISWA SAJA) 🌟
    // ==========================================================
    public function processCheckout(Request $request)
    {
        $cart = session()->get('cart');

        if (!$cart || count($cart) == 0) {
            return back()->with('error', 'Keranjangmu kosong!');
        }

        // TANGGAL SUDAH DIHAPUS DARI VALIDASI CHECKOUT (Karena sudah dicek di awal)
        $request->validate([
            'full_name' => 'required|string',
            'organization' => 'required|string',
            'position' => 'required|string',
            'phone_number' => 'required|string',
            'proker_name' => 'required|string',
            'treasurer_name' => 'required|string',
            'address' => 'required|string',
            'is_sop_accepted' => 'required|accepted',
        ]);

        $firstItem = reset($cart);
        $orderType = $firstItem['transaction_type'];

        // 🌟 KITA CARI TANGGAL PALING AWAL DAN PALING AKHIR DARI KERANJANG 🌟
        $globalStartDate = null;
        $globalEndDate = null;
        $totalPrice = 0;

        foreach ($cart as $id => $item) {
            // Set Global Date untuk header Order
            if ($item['start_date']) {
                if (is_null($globalStartDate) || $item['start_date'] < $globalStartDate) {
                    $globalStartDate = $item['start_date'];
                }
                if (is_null($globalEndDate) || $item['end_date'] > $globalEndDate) {
                    $globalEndDate = $item['end_date'];
                }
            }

            // Hitung harga
            $dbItem = Item::find($id);
            $unitPrice = $item['price'];
            if ($request->organization === 'Student Council' && str_contains($dbItem->transaction_type, 'Internal')) {
                $unitPrice = 0; // SC Internal = Gratis!
            }
            $totalPrice += ($unitPrice * $item['quantity']);
        }

        DB::beginTransaction();

        try {
            // 🛡️ THE GATEKEEPER: CEK OVERLAP ULANG (JAGA-JAGA ADA YG ORDER DULUAN PAS DIA DI KERANJANG)
            foreach ($cart as $id => $item) {
                $dbItem = Item::lockForUpdate()->find($id);
                
                if ($orderType === 'Sale') {
                    if (!$dbItem || $dbItem->stock_quantity < $item['quantity']) {
                        DB::rollBack();
                        return back()->with('error', "Gagal! Stok '{$item['name']}' keburu habis dibeli orang.");
                    }
                    $dbItem->decrement('stock_quantity', $item['quantity']);
                } else {
                    // Cek Ulang Rental Overlap
                    $overlappingQty = OrderItem::where('item_id', $id)
                        ->whereHas('order', function ($query) use ($item) {
                            $query->whereNotIn('status', ['Returned', 'Resolved (Fine Paid)', 'Rejected', 'Cancelled'])
                                  ->where('order_type', '!=', 'Sale')
                                  ->where('start_date', '<=', $item['end_date'])
                                  ->where('end_date', '>=', $item['start_date']);
                        })->sum('quantity');

                    if (($overlappingQty + $item['quantity']) > $dbItem->stock_quantity) {
                        DB::rollBack();
                        return back()->with('error', "Maaf, barang '{$item['name']}' baru saja dibooking orang lain di tanggal pilihanmu. Sisa stok tidak cukup.");
                    }
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
                'department' => '-',
                'treasurer_name' => $request->treasurer_name,
                'address' => $request->address,
                'order_type' => $orderType,
                'start_date' => $globalStartDate, // Diambil otomatis dari keranjang
                'end_date' => $globalEndDate,     // Diambil otomatis dari keranjang
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

            // 🌟 TEMBAK NOTIFIKASI ADMIN 🌟
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new AdminNotification('Order Baru: ' . $order->order_number . ' dari ' . $order->proker_name));
            }

            DB::commit();

            return redirect()->route('student.loans')->with('success', 'Checkout berhasil! Kuitansimu sedang menunggu persetujuan Admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}