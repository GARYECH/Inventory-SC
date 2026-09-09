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

        // Tentukan apakah kategori ini butuh tanggal (Rental/Reusable) atau tidak (Consumable/Sale)
        // Kategori yang butuh tanggal: Peralatan, HT UV-82, HT 888s, HT UV-5R
        // Kategori habis pakai/beli putus: ATK, Obat, Merchandise
        $requiresDate = in_array($item->transaction_type, ['Peralatan', 'HT UV-82', 'HT 888s', 'HT UV-5R']);

        // 1. STRICT CART LOGIC: CEK CAMPUR KATEGORI
        if (count($cart) > 0) {
            $firstItem = reset($cart);
            if ($firstItem['transaction_type'] !== $item->transaction_type) {
                return back()->with('error', "Mimpi buruk database dicegah! 🚫 Kamu tidak bisa mencampur kategori '{$firstItem['transaction_type']}' dengan '{$item->transaction_type}' dalam satu keranjang. Selesaikan atau kosongkan keranjangmu dulu!");
            }
        }

        // 2. LOGIKA PENGECEKAN STOK & JADWAL
        if ($requiresDate) {
            // Wajib kirim tanggal dari form add to cart untuk barang sewa
            $request->validate([
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
            ], [
                'start_date.required' => 'Pilih tanggal mulai sewa terlebih dahulu!',
                'end_date.required' => 'Pilih tanggal selesai sewa terlebih dahulu!',
            ]);

            $startDate = $request->start_date;
            $endDate = $request->end_date;

            // Hitung barang yang overlap di rentang tanggal tersebut
            $overlappingQty = OrderItem::where('item_id', $item->id)
                ->whereHas('order', function ($query) use ($startDate, $endDate) {
                    $query->whereNotIn('status', ['Returned', 'Resolved (Fine Paid)', 'Rejected', 'Cancelled'])
                          ->where('start_date', '<=', $endDate) 
                          ->where('end_date', '>=', $startDate); 
                })->sum('quantity');

            // Hitung barang yang sudah ada di keranjang sesi ini
            $qtyInCart = isset($cart[$item->id]) ? $cart[$item->id]['quantity'] : 0;
            $totalDiminta = $overlappingQty + $qtyInCart + $requestQuantity;

            // Jika melebihi kapasitas gudang
            if ($totalDiminta > $item->stock_quantity) {
                $sisaKuota = max(0, $item->stock_quantity - $overlappingQty - $qtyInCart);
                $formatStart = Carbon::parse($startDate)->format('d M');
                $formatEnd = Carbon::parse($endDate)->format('d M');
                return back()->with('error', "Gagal! Untuk tanggal {$formatStart} - {$formatEnd}, sisa stok '{$item->name}' hanya {$sisaKuota} unit.");
            }
        } else {
            // Logika untuk Barang Habis Pakai (ATK, Obat, Merchandise) tanpa tanggal
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
            $requiresDate = in_array($item->transaction_type, ['Peralatan', 'HT UV-82', 'HT 888s', 'HT UV-5R']);

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

        // Validasi input tambahan (ketua_acara & notes)
        $request->validate([
            'full_name' => 'required|string',
            'organization' => 'required|string',
            'position' => 'required|string',
            'phone_number' => 'required|string',
            'proker_name' => 'required|string',
            'ketua_acara' => 'required|string', // 🌟 Validasi Nama Ketua Acara
            'treasurer_name' => 'required|string',
            'address' => 'required|string',
            'notes' => 'nullable|string',       // 🌟 Validasi Catatan Opsional
            'is_sop_accepted' => 'required|accepted',
        ]);

        $firstItem = reset($cart);
        $orderType = $firstItem['transaction_type'];
        $isConsumable = in_array($orderType, ['ATK', 'Obat', 'Merchandise']);

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
            // Jika Organisasi Student Council dan tipenya Peralatan/HT (Internal), maka gratis
            if ($request->organization === 'Student Council' && in_array($dbItem->transaction_type, ['Peralatan', 'HT UV-82', 'HT 888s', 'HT UV-5R'])) {
                $unitPrice = 0; 
            }
            $totalPrice += ($unitPrice * $item['quantity']);
        }

        DB::beginTransaction();

        try {
            // 🛡️ GATEKEEPER: CEK STOK & OVERLAP ULANG SEBELUM COMMIT
            foreach ($cart as $id => $item) {
                $dbItem = Item::lockForUpdate()->find($id);
                
                if ($isConsumable) {
                    // Barang Habis Pakai / Merchandise langsung potong stok permanen
                    if (!$dbItem || $dbItem->stock_quantity < $item['quantity']) {
                        DB::rollBack();
                        return back()->with('error', "Gagal! Stok '{$item['name']}' keburu habis.");
                    }
                    $dbItem->decrement('stock_quantity', $item['quantity']);
                } else {
                    // Cek Ulang Rental Overlap
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
                'ketua_acara' => $request->ketua_acara, // 🌟 Simpan Ketua Acara
                'department' => '-',
                'treasurer_name' => $request->treasurer_name,
                'address' => $request->address,
                'notes' => $request->notes,             // 🌟 Simpan Catatan Peminjam
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
                if ($request->organization === 'Student Council' && in_array($dbItem->transaction_type, ['Peralatan', 'HT UV-82', 'HT 888s', 'HT UV-5R'])) {
                    $unitPrice = 0;
                }

                OrderItem::create([
                    'order_number' => $order->id, // Jika kolom relasi di database pakai order_id sesuaikan, dibiarkan seperti struktur asal
                    'order_id' => $order->id,
                    'item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $unitPrice,
                    'subtotal_price' => ($unitPrice * $item['quantity']),
                ]);
            }

            session()->forget('cart');

            // 🌟 NOTIFIKASI KE ADMIN 🌟
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