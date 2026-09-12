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
    // 🌟 ADD TO CART DENGAN STRICT CART & PENGECEKAN STOK 🌟
    // ==========================================================
    public function addToCart(Request $request, Item $item)
    {
        $cart = session()->get('cart', []);
        $requestQuantity = $request->quantity ?? 1;

        // 🌟 FIX: ATK sekarang dimasukkan ke kategori yang wajib kalender/tanggal
        $requiresDate = in_array($item->transaction_type, ['Peralatan', 'ATK', 'HT UV-82', 'HT 888s', 'HT UV-5R']);

        // 1. STRICT CART LOGIC: CEK CAMPUR KATEGORI
        if (count($cart) > 0) {
            $firstItem = reset($cart);
            if ($firstItem['transaction_type'] !== $item->transaction_type) {
                return back()->with('error', "Mimpi buruk database dicegah! 🚫 Kamu tidak bisa mencampur kategori '{$firstItem['transaction_type']}' dengan '{$item->transaction_type}' dalam satu keranjang. Selesaikan atau kosongkan keranjangmu dulu!");
            }
        }

        // 2. LOGIKA PENGECEKAN STOK & JADWAL
        if ($requiresDate) {
            $request->validate([
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
            ], [
                'start_date.required' => 'Pilih tanggal mulai peminjaman terlebih dahulu!',
                'end_date.required' => 'Pilih tanggal selesai peminjaman terlebih dahulu!',
            ]);

            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $overlappingQty = OrderItem::where('item_id', $item->id)
                ->whereHas('order', function ($query) use ($startDate, $endDate) {
                    $query->whereNotIn('status', ['Returned', 'Resolved (Fine Paid)', 'Rejected', 'Cancelled'])
                          ->where('start_date', '<=', $endDate) 
                          ->where('end_date', '>=', $startDate); 
                })->sum('quantity');

            $qtyInCart = isset($cart[$item->id]) ? $cart[$item->id]['quantity'] : 0;
            $totalDiminta = $overlappingQty + $qtyInCart + $requestQuantity;

            if ($totalDiminta > $item->stock_quantity) {
                $sisaKuota = max(0, $item->stock_quantity - $overlappingQty - $qtyInCart);
                $formatStart = Carbon::parse($startDate)->format('d M');
                $formatEnd = Carbon::parse($endDate)->format('d M');
                return back()->with('error', "Gagal! Untuk tanggal {$formatStart} - {$formatEnd}, sisa stok '{$item->name}' hanya {$sisaKuota} unit.");
            }
        } else {
            // Logika untuk Barang Habis Pakai murni (Obat, Kertas, Merchandise)
            $qtyInCart = isset($cart[$item->id]) ? $cart[$item->id]['quantity'] : 0;
            if (($qtyInCart + $requestQuantity) > $item->stock_quantity) {
                return back()->with('error', "Gagal! Stok gudang tidak mencukupi. Sisa stok: {$item->stock_quantity} unit.");
            }
            $startDate = null;
            $endDate = null;
        }

        // 3. MASUKKAN KE KERANJANG SESI
        if (isset($cart[$item->id])) {
            $cart[$item->id]['quantity'] += $requestQuantity;
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
                'start_date' => $startDate,
                'end_date' => $endDate,
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

    // ==========================================================
    // 🌟 UPDATE QTY DI HALAMAN CHECKOUT 🌟
    // ==========================================================
    public function updateCart(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$id])) {
            $item = Item::findOrFail($id);
            $newQty = $request->quantity;
            
            // 🌟 FIX: ATK masuk kategori pengecekan kalender
            $requiresDate = in_array($item->transaction_type, ['Peralatan', 'ATK', 'HT UV-82', 'HT 888s', 'HT UV-5R']);

            if ($requiresDate) {
                $startDate = $cart[$id]['start_date'];
                $endDate = $cart[$id]['end_date'];

                $overlappingQty = OrderItem::where('item_id', $item->id)
                    ->whereHas('order', function ($query) use ($startDate, $endDate) {
                        $query->whereNotIn('status', ['Returned', 'Resolved (Fine Paid)', 'Rejected', 'Cancelled'])
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

            $cart[$id]['quantity'] = $newQty;
            session()->put('cart', $cart);
            return back()->with('success', 'Jumlah barang berhasil diupdate!');
        }

        return back()->with('error', 'Barang tidak ditemukan di keranjang.');
    }

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
    // 🌟 FASE CHECKOUT & PEMBUATAN ORDER (DENGAN NOTES & KETUA ACARA) 🌟
    // ==========================================================
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
            'ketua_acara' => 'required|string',
            'treasurer_name' => 'required|string',
            'address' => 'required|string',
            'notes' => 'nullable|string',
            'is_sop_accepted' => 'required|accepted',
        ]);

        $firstItem = reset($cart);
        $orderType = $firstItem['transaction_type'];
        
        // 🌟 FIX: ATK dikeluarkan dari consumable, diganti dengan Kertas (jika ada)
        $isConsumable = in_array($orderType, ['Obat', 'Kertas', 'Merchandise']);

        $globalStartDate = null;
        $globalEndDate = null;
        $totalPrice = 0;

        foreach ($cart as $id => $item) {
            if (!empty($item['start_date'])) {
                if (is_null($globalStartDate) || $item['start_date'] < $globalStartDate) {
                    $globalStartDate = $item['start_date'];
                }
                if (is_null($globalEndDate) || $item['end_date'] > $globalEndDate) {
                    $globalEndDate = $item['end_date'];
                }
            }

            $dbItem = Item::find($id);
            $unitPrice = $item['price'];
            
            // 🌟 FIX: Pastikan SC HANYA gratis untuk Peralatan dan HT UV-5R
            if ($request->organization === 'Student Council' && in_array($dbItem->transaction_type, ['Peralatan', 'HT UV-5R'])) {
                $unitPrice = 0; 
            }
            $totalPrice += ($unitPrice * $item['quantity']);
        }

        DB::beginTransaction();

        try {
            foreach ($cart as $id => $item) {
                $dbItem = Item::lockForUpdate()->find($id);
                
                if ($isConsumable) {
                    if (!$dbItem || $dbItem->stock_quantity < $item['quantity']) {
                        DB::rollBack();
                        return back()->with('error', "Gagal! Stok '{$item['name']}' keburu habis.");
                    }
                    $dbItem->decrement('stock_quantity', $item['quantity']);
                } else {
                    $overlappingQty = OrderItem::where('item_id', $id)
                        ->whereHas('order', function ($query) use ($item) {
                            $query->whereNotIn('status', ['Returned', 'Resolved (Fine Paid)', 'Rejected', 'Cancelled'])
                                  ->where('start_date', '<=', $item['end_date'])
                                  ->where('end_date', '>=', $item['start_date']);
                        })->sum('quantity');

                    if (($overlappingQty + $item['quantity']) > $dbItem->stock_quantity) {
                        DB::rollBack();
                        return back()->with('error', "Maaf, barang '{$item['name']}' baru saja dibooking orang lain di tanggal pilihanmu.");
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
                'ketua_acara' => $request->ketua_acara,
                'department' => '-',
                'treasurer_name' => $request->treasurer_name,
                'address' => $request->address,
                'notes' => $request->notes,
                'order_type' => $orderType,
                'start_date' => $globalStartDate,
                'end_date' => $globalEndDate,
                'is_sop_accepted' => true,
                'total_price' => $totalPrice, 
                'status' => 'Pending',
            ]);

            foreach ($cart as $item) {
                $dbItem = Item::find($item['id']);
                $unitPrice = $item['price'];
                
                // 🌟 FIX: Potongan harga order item juga hanya untuk Peralatan & HT UV-5R
                if ($request->organization === 'Student Council' && in_array($dbItem->transaction_type, ['Peralatan', 'HT UV-5R'])) {
                    $unitPrice = 0;
                }

                OrderItem::create([
                    'order_number' => $order->id, 
                    'order_id' => $order->id,
                    'item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $unitPrice,
                    'subtotal_price' => ($unitPrice * $item['quantity']),
                ]);
            }

            session()->forget('cart');

            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new AdminNotification('Order Baru: ' . $order->order_number . ' dari ' . $order->proker_name));
            }

            DB::commit();

            return redirect()->route('student.loans')->with('success', 'Checkout berhasil! Pengajuanmu sedang menunggu persetujuan Admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}