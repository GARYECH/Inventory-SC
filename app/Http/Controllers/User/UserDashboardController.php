<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    /**
     * Menampilkan katalog barang yang tersedia untuk disewa.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $items = Item::where('condition_status', 'Good')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(8)
            ->withQueryString();
        
        return view('user.dashboard', compact('items'));
    }

    /**
     * Menampilkan daftar pinjaman aktif dan riwayat masa lalu secara terpisah.
     */
    public function loans()
    {
        $today = now()->toDateString();

        // 1. ACTIVE LOANS: Hanya yang sedang berjalan atau akan datang (PENTING!)
        $activeLoans = Order::where('user_id', auth()->id())
            ->where('end_date', '>=', $today) 
            ->whereIn('status', ['Pending', 'Approved', 'Borrowed'])
            ->with('item')
            ->latest()
            ->get();

        // 2. PAST HISTORY: Untuk arsip mahasiswa (Hanya tampilkan jika sudah selesai/lewat)
        $pastLoans = Order::where('user_id', auth()->id())
            ->where(function ($query) use ($today) {
                $query->where('end_date', '<', $today)
                      ->orWhere('status', 'Returned')
                      ->orWhere('status', 'Cancelled');
            })
            ->with('item')
            ->latest()
            ->paginate(5);

        // Kirim keduanya ke view
        return view('user.loans', compact('activeLoans', 'pastLoans'));
    }
}