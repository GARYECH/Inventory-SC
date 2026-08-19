<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    /**
     * 🛒 Menampilkan katalog barang (Dengan 3 Jalur Navigasi)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type'); // Kosong = Menampilkan Semua Barang

        $query = Item::where('condition_status', 'Good')
            ->with(['orderItems.order' => function($q) {
                $q->whereNotIn('status', ['Returned', 'Resolved (Fine Paid)', 'Rejected', 'Cancelled'])
                  ->where('end_date', '>=', now()->toDateString()) // Hanya jadwal hari ini & ke depan
                  ->orderBy('start_date', 'asc');
            }]);

        // 🌟 JIKA ADA TAB YANG DIKLIK, BARU FILTER KATEGORINYA
        if ($type) {
            $query->where('transaction_type', $type);
        }

        // 🌟 LOGIKA PENCARIAN
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $items = $query->latest()->paginate(12)->withQueryString();
        
        // Cek isi keranjang saat ini untuk memunculkan notifikasi angka
        $cartCount = count(session()->get('cart', []));
        
        return view('user.dashboard', compact('items', 'type', 'cartCount'));
    }

    /**
     * 📜 Menampilkan riwayat transaksi mahasiswa
     */
    public function loans()
    {
        // 1. ACTIVE ORDERS: Semua kuitansi yang aktif berjalan
        $activeLoans = Order::where('user_id', auth()->id())
            ->whereNotIn('status', ['Returned', 'Resolved (Fine Paid)', 'Cancelled', 'Rejected'])
            ->with('orderItems.item') 
            ->latest()
            ->get();

        // 2. PAST HISTORY: Untuk arsip mahasiswa (Selesai / Batal / Ditolak)
        $pastLoans = Order::where('user_id', auth()->id())
            ->whereIn('status', ['Returned', 'Resolved (Fine Paid)', 'Cancelled', 'Rejected'])
            ->with('orderItems.item')
            ->latest()
            ->paginate(5); 

        return view('user.loans', compact('activeLoans', 'pastLoans'));
    }

    /**
     * 🗓️ Menampilkan jadwal detail suatu barang
     */
    public function itemSchedule($id)
    {
        $item = Item::findOrFail($id);

        // Ambil semua transaksi aktif untuk barang ini (Selain yang sudah dikembalikan/batal/beli putus)
        $activeBookings = OrderItem::where('item_id', $id)
            ->whereHas('order', function ($query) {
                $query->whereNotIn('status', ['Returned', 'Resolved (Fine Paid)', 'Rejected', 'Cancelled'])
                      ->where('order_type', '!=', 'Sale');
            })
            ->with('order.user') // Load data order dan user
            ->get()
            ->sortBy(function ($orderItem) {
                return $orderItem->order->start_date; // Urutkan dari tanggal terdekat
            });

        return view('user.item_schedule', compact('item', 'activeBookings'));
    }
}