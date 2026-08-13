<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
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
            ->paginate(5); // 🌟 Hapus get(), langsung paginate()

        return view('user.loans', compact('activeLoans', 'pastLoans'));
    }
}