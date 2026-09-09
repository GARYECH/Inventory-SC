<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    /**
     * 🛒 Menampilkan katalog barang (Dengan Tab Premium Baru)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type'); // Tab Kategori

        // Hanya tampilkan barang yang 'Good'
        $query = Item::where('condition_status', 'Good')
            ->with(['orderItems.order' => function($q) {
                $q->whereNotIn('status', ['Returned', 'Resolved (Fine Paid)', 'Rejected', 'Cancelled'])
                  ->where('end_date', '>=', now()->toDateString())
                  ->orderBy('start_date', 'asc');
            }]);

        // 🌟 FITUR FILTER KATEGORI BARU (Peralatan, HT, HabisPakai, Merchandise)
        if ($type) {
            if ($type === 'HT') {
                $query->whereIn('transaction_type', ['HT UV-82', 'HT 888s', 'HT UV-5R']);
            } elseif ($type === 'HabisPakai') {
                $query->whereIn('transaction_type', ['ATK', 'Obat']);
            } else {
                $query->where('transaction_type', $type);
            }
        }

        // 🌟 LOGIKA PENCARIAN
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Paginasi 12 item
        $items = $query->latest()->paginate(12)->appends($request->query());
        
        $cartCount = count(session()->get('cart', []));
        
        return view('user.dashboard', compact('items', 'type', 'cartCount'));
    }

    /**
     * 📜 Menampilkan riwayat transaksi mahasiswa
     */
    public function loans()
    {
        $activeLoans = Order::where('user_id', auth()->id())
            ->whereNotIn('status', ['Returned', 'Resolved (Fine Paid)', 'Cancelled', 'Rejected'])
            ->with('orderItems.item') 
            ->latest()
            ->get();

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

        // 🌟 UPDATE: Jangan tampilkan barang beli putus/habis pakai di jadwal
        $activeBookings = OrderItem::where('item_id', $id)
            ->whereHas('order', function ($query) {
                $query->whereNotIn('status', ['Returned', 'Resolved (Fine Paid)', 'Rejected', 'Cancelled'])
                      ->whereNotIn('order_type', ['ATK', 'Obat', 'Merchandise']);
            })
            ->with('order.user')
            ->get()
            ->sortBy(function ($orderItem) {
                return $orderItem->order->start_date;
            });

        return view('user.item_schedule', compact('item', 'activeBookings'));
    }

   /**
    * 🌟 API KALENDER TRAVELOKA (FIX AKURASI RENTANG TANGGAL) 🌟
    */
    public function checkStock($id)
    {
        $item = Item::findOrFail($id);
        $totalStock = $item->stock_quantity;

        // 🌟 UPDATE: Abaikan orderan tipe ATK, Obat, Merchandise karena stoknya lgsg potong
        $activeLoans = OrderItem::where('item_id', $id)
            ->whereHas('order', function ($query) {
                $query->whereNotIn('status', ['Returned', 'Resolved (Fine Paid)', 'Rejected', 'Cancelled'])
                      ->whereNotIn('order_type', ['ATK', 'Obat', 'Merchandise']);
            })->get();

        $availability = [];
        $startDate = Carbon::today()->subDays(7);
        
        for ($i = 0; $i < 90; $i++) {
            $date = $startDate->copy()->addDays($i)->toDateString();
            $bookedToday = 0;

            foreach ($activeLoans as $loan) {
                if ($loan->order->start_date && $loan->order->end_date) {
                    $loanStart = Carbon::parse($loan->order->start_date)->toDateString();
                    $loanEnd = Carbon::parse($loan->order->end_date)->toDateString();

                    if ($date >= $loanStart && $date <= $loanEnd) {
                        $bookedToday += $loan->quantity;
                    }
                }
            }

            $sisa = $totalStock - $bookedToday;
            $availability[$date] = max(0, $sisa);
        }

        return response()->json($availability);
    }
}