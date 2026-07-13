<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class RentalController extends Controller
{
    /**
     * Menampilkan halaman reservasi dengan fitur transparansi stok.
     */
    public function create(Item $item)
    {
        // TRANSPARENCY CHECK: Mengambil semua booking aktif agar mahasiswa bisa janjian
        $existingReservations = Order::where('item_id', $item->id)
            ->whereIn('status', ['Pending', 'Approved', 'Borrowed'])
            ->where('end_date', '>=', now()->toDateString()) // Hanya tampilkan yang belum lewat
            ->with('user')
            ->orderBy('start_date', 'asc')
            ->get();

        return view('user.rentals.create', compact('item', 'existingReservations'));
    }

    /**
     * Memproses reservasi dengan validasi stok virtual yang ketat.
     */
    public function store(Request $request, Item $item)
    {
        // 1. VALIDASI INPUT DASAR
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ], [
            'end_date.after' => 'The end date must be at least one day after the start date, mon cher.',
            'start_date.after_or_equal' => 'You cannot rent gear in the past!',
        ]);

        try {
            // 2. LOGIKA STOK VIRTUAL (The Heart of the System)
            // Kita hitung jumlah barang yang terpakai pada rentang tanggal yang dipilih
            $overlapCount = Order::where('item_id', $item->id)
                ->whereIn('status', ['Pending', 'Approved', 'Borrowed'])
                ->where(function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        // Kasus 1: Tanggal mulai atau selesai berada di dalam rentang booking orang lain
                        $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                          ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
                    })->orWhere(function ($q) use ($request) {
                        // Kasus 2: Booking baru "menelan" rentang booking orang lain secara utuh
                        $q->where('start_date', '<=', $request->start_date)
                          ->where('end_date', '>=', $request->end_date);
                    });
                })
                ->sum('quantity');

            // Hitung sisa stok yang benar-benar tersedia
            $remainingStock = $item->stock_quantity - $overlapCount;

            // 3. EXCEPTION: Stok tidak mencukupi
            if ($request->quantity > $remainingStock) {
                return back()->withInput()->with('error', 
                    "Stock Conflict! For the selected dates, only $remainingStock unit(s) left. Please adjust your quantity or dates.");
            }

            // 4. DATABASE TRANSACTION: Menjamin data tersimpan dengan sempurna
            DB::transaction(function () use ($request, $item) {
                Order::create([
                    'order_number' => 'RES-' . strtoupper(Str::random(8)),
                    'user_id' => auth()->id(),
                    'item_id' => $item->id,
                    'quantity' => $request->quantity,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'total_price' => $item->price * $request->quantity,
                    'status' => 'Pending',
                ]);
            });

            return redirect()->route('student.dashboard')->with('success', 'Votre reservation a été envoyée! Wait for Admin approval.');

        } catch (Exception $e) {
            // 5. GLOBAL EXCEPTION HANDLING: Menangkap error tak terduga (misal: database mati)
            return back()->with('error', "Something went wrong on the server. Please try again later, mon cher.");
        }
    }

    /**
     * Membatalkan pesanan (Hanya jika masih Pending).
     */
    public function cancel(Order $order)
    {
        try {
            // SECURITY CHECK: Pastikan user hanya bisa membatalkan milik sendiri
            if ($order->user_id !== auth()->id()) {
                throw new Exception("Unauthorized cancellation attempt.");
            }

            // BUSINESS LOGIC CHECK: Pesanan hanya bisa dibatalkan jika belum di-approve Admin
            if ($order->status !== 'Pending') {
                return back()->with('error', "Order is already being processed and cannot be cancelled. Contact Admin, mon cher.");
            }

            $order->update(['status' => 'Cancelled']);

            return back()->with('success', 'Order cancelled. The stock has been released for others!');

        } catch (Exception $e) {
            return back()->with('error', "Failed to cancel order. Please refresh and try again.");
        }
    }
}