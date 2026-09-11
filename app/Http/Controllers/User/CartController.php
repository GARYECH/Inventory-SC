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
        $size = $request->size ?? null; // 🌟 BARU: Tangkap Input Size

        $requiresDate = in_array($item->transaction_type, ['Peralatan', 'HT UV-82', 'HT 888s', 'HT UV-5R']);

        if (count($cart) > 0) {
            $firstItem = reset($cart);
            if ($firstItem['transaction_type'] !== $item->transaction_type) {
                return back()->with('error', "Mimpi buruk database dicegah! 🚫 Kamu tidak bisa mencampur kategori '{$firstItem['transaction_type']}' dengan '{$item->transaction_type}' dalam satu keranjang.");
            }
        }

        // 🌟 BARU: LOGIKA HARGA TAMBAHAN UNTUK BAJU SIZE JUMBO 🌟
        $extraPrice = 0;
        if ($item->transaction_type === 'Merchandise' || str_contains(strtolower($item->name), 'baju')) {
            switch ($size) {
                case '2XL': $extraPrice = 5000; break;
                case '3XL': $extraPrice = 10000; break;
                case '4XL': $extraPrice = 15000; break;
                case '5XL': $extraPrice = 20000; break;
                default: $extraPrice = 0;
            }
        }
        $finalPrice = $item->price + $extraPrice;

        // 🌟 BARU: Bikin ID Cart Unik agar size berbeda tidak tumpuk di 1 baris
        $cartKey = $size ? $item->id . '-' . $size : $item->id;

        if ($requiresDate) {
            $request->validate([
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
            ], [
                'start_date.required' => 'Pilih tanggal mulai sewa terlebih dahulu!',
                'end_date.required' => 'Pilih tanggal selesai sewa terlebih dahulu!',
            ]);

            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $overlappingQty = OrderItem::where('item_id', $item->id)
                ->whereHas('order', function ($query) use ($startDate, $endDate) {
                    $query->whereNotIn('status', ['Returned', 'Resolved (Fine Paid)', 'Rejected', 'Cancelled'])
                          ->where('start_date', '<=', $endDate) 
                          ->where('end_date', '>=', $startDate); 
                })->sum('quantity');

            $qtyInCart = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
            $totalDiminta = $overlappingQty + $qtyInCart + $requestQuantity;

            if ($totalDiminta > $item->stock_quantity) {
                $sisaKuota = max(0, $item->stock_quantity - $overlappingQty - $qtyInCart);
                $formatStart = Carbon::parse($startDate)->format('d M');
                $formatEnd = Carbon::parse($endDate)->format('d M');
                return back()->with('error', "Gagal! Untuk tanggal {$formatStart} - {$formatEnd}, sisa stok '{$item->name}' hanya {$sisaKuota} unit.");
            }
        } else {
            $qtyInCart = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
            if (($qtyInCart + $requestQuantity) > $item->stock_quantity) {
                return back()->with('error', "Gagal! Stok gudang tidak mencukupi. Sisa stok: {$item->stock_quantity} unit.");
            }
            $startDate = null;
            $endDate = null;
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $requestQuantity;
            $cart[$cartKey]['start_date'] = $startDate;
            $cart[$cartKey]['end_date'] = $endDate;
        } else {
            $cart[$cartKey] = [
                'id' => $item->id, // Real Item ID untuk Database
                'name' => $size ? $item->name . ' (' . $size . ')' : $item->name, // Nama otomatis ada sizenya
                'price' => $finalPrice, // Harga sudah +biaya jumbo
                'quantity' => $requestQuantity,
                'size' => $size, // 🌟 BARU: Simpan size
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
    public function updateCart(Request $request, $cartKey)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$cartKey])) {
            // 🌟 FIX: Ambil ID Item asli dari dalam cart, bukan dari parameter $cartKey
            $realItemId = $cart[$cartKey]['id'];
            $item = Item::findOrFail($realItemId);
            
            $newQty = $request->quantity;
            $requiresDate = in_array($item->transaction_type, ['Peralatan', 'HT UV-82', 'HT 888s', 'HT UV-5R']);

            if ($requiresDate) {
                $startDate = $cart[$cartKey]['start_date'];
                $endDate = $cart[$cartKey]['end_date'];

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

            $cart[$cartKey]['quantity'] = $newQty;
            session()->put('cart', $cart);
            return back()->with('success', 'Jumlah barang berhasil diupdate!');
        }

        return back()->with('error', 'Barang tidak ditemukan di keranjang.');
    }

    public function removeItem($cartKey)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            session()->put('cart', $cart);
        }
        return back()->with('success', 'Barang berhasil dihapus dari keranjang.');
    }

    // ==========================================================
    // 🌟 FASE CHECKOUT & PEMBUATAN ORDER (LOGIKA HARI RENTAL) 🌟
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
            'design_link' => 'nullable|url', // 🌟 BARU: Validasi link drive
            'is_sop_accepted' => 'required|accepted',
        ]);

        $firstItem = reset($cart);
        $orderType = $firstItem['transaction_type'];
        $isConsumable = in_array($orderType, ['ATK', 'Obat', 'Merchandise']);

        $globalStartDate = null;
        $globalEndDate = null;
        $totalPrice = 0;

        // 🌟 LOOP 1: KALKULASI HARGA & HARI 🌟
        foreach ($cart as $cartKey => $item) {
            if (!empty($item['start_date'])) {
                if (is_null($globalStartDate) || $item['start_date'] < $globalStartDate) {
                    $globalStartDate = $item['start_date'];
                }
                if (is_null($globalEndDate) || $item['end_date'] > $globalEndDate) {
                    $globalEndDate = $item['end_date'];
                }
            }

            $dbItem = Item::find($item['id']); // 🌟 FIX: Gunakan $item['id'] bukan $cartKey
            $unitPrice = $item['price'];
            $rentalDays = 1; 
            
            if (in_array($dbItem->transaction_type, ['Peralatan', 'HT UV-82', 'HT 888s', 'HT UV-5R']) && !empty($item['start_date']) && !empty($item['end_date'])) {
                $start = Carbon::parse($item['start_date']);
                $end = Carbon::parse($item['end_date']);
                $diffDays = $start->diffInDays($end);
                $rentalDays = max(1, $diffDays - 1);
            }

            if ($request->organization === 'Student Council' && in_array($dbItem->transaction_type, ['Peralatan', 'HT UV-5R'])) {
                $unitPrice = 0; 
            }

            $cart[$cartKey]['calc_unit_price'] = $unitPrice;
            $cart[$cartKey]['calc_rental_days'] = $rentalDays;
            $cart[$cartKey]['calc_subtotal'] = $unitPrice * $item['quantity'] * $rentalDays;
            
            $totalPrice += $cart[$cartKey]['calc_subtotal'];
        }

        DB::beginTransaction();

        try {
            // 🛡️ GATEKEEPER: CEK STOK & OVERLAP ULANG SEBELUM COMMIT
            foreach ($cart as $cartKey => $item) {
                $dbItem = Item::lockForUpdate()->find($item['id']); // 🌟 FIX: Gunakan $item['id']
                
                if ($isConsumable) {
                    if (!$dbItem || $dbItem->stock_quantity < $item['quantity']) {
                        DB::rollBack();
                        return back()->with('error', "Gagal! Stok '{$item['name']}' keburu habis.");
                    }
                    $dbItem->decrement('stock_quantity', $item['quantity']);
                } else {
                    $overlappingQty = OrderItem::where('item_id', $item['id']) // 🌟 FIX: Gunakan $item['id']
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

            // 🌟 CREATE ORDER HEADER 🌟
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
                'design_link' => $request->design_link, // 🌟 BARU: Simpan link G-Drive
                'order_type' => $orderType,
                'start_date' => $globalStartDate,
                'end_date' => $globalEndDate,
                'is_sop_accepted' => true,
                'total_price' => $totalPrice, 
                'status' => 'Pending',
            ]);

            // 🌟 CREATE ORDER DETAIL / ITEMS 🌟
            foreach ($cart as $cartKey => $item) {
                OrderItem::create([
                    'order_number' => $order->id, 
                    'order_id' => $order->id,
                    'item_id' => $item['id'], // 🌟 FIX: Gunakan $item['id'] agar DB aman
                    'size' => $item['size'] ?? null, // 🌟 BARU: Simpan size ke detail order
                    'quantity' => $item['quantity'],
                    'price' => $item['calc_unit_price'], 
                    'subtotal_price' => $item['calc_subtotal'], 
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