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
        
        // 🌟 TIGA JALUR NAVIGASI: Default ke Internal Rental jika tidak ada filter
        $type = $request->input('type', 'Internal Rental'); 

        $items = Item::where('condition_status', 'Good')
            ->where('transaction_type', $type) // 🔒 Filter ketat sesuai tab yang dipilih
            // 🌟 TAMBAHAN: Load data order yang sedang aktif / belum selesai untuk jadwal
            ->with(['orderItems.order' => function($q) {
                $q->whereNotIn('status', ['Returned', 'Rejected', 'Cancelled'])
                  ->where('end_date', '>=', now()->toDateString()) // Hanya jadwal hari ini & ke depan
                  ->orderBy('start_date', 'asc');
            }])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(8)
            ->withQueryString();
        
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
            ->whereNotIn('status', ['Returned', 'Cancelled', 'Rejected'])
            ->with('orderItems.item') 
            ->latest()
            ->get();

        // 2. PAST HISTORY: Untuk arsip mahasiswa (Selesai / Batal / Ditolak)
        $pastLoans = Order::where('user_id', auth()->id())
            ->whereIn('status', ['Returned', 'Cancelled', 'Rejected'])
            ->with('orderItems.item')
            ->latest()
            ->paginate(5); 

        return view('user.loans', compact('activeLoans', 'pastLoans'));
    }
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