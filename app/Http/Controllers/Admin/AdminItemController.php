<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Notifications\OrderStatusUpdated;

class AdminItemController extends Controller
{
    // =================================================================
    // 📦 ITEM MANAGEMENT (GUDANG)
    // =================================================================

   public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');

        // 🌟 UPDATE COUNTS (Menghitung 4 Kategori Baru + Kompatibel Arsip Lama)
        $counts = [
            'total' => Item::count(),
            'peralatan' => Item::whereIn('transaction_type', ['Peralatan', 'Internal Rental', 'Vendor Rental'])->count(),
            'ht' => Item::whereIn('transaction_type', ['HT UV-82', 'HT 888s', 'HT UV-5R'])->count(),
            'habispakai' => Item::whereIn('transaction_type', ['ATK', 'Obat'])->count(),
            'merchandise' => Item::whereIn('transaction_type', ['Merchandise', 'Sale'])->count(),
        ];

        $items = Item::with('category')
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($type, function ($query, $type) {
                // 🌟 UPDATE FILTER TAB LOGIC
                if ($type === 'Peralatan') {
                    return $query->whereIn('transaction_type', ['Peralatan', 'Internal Rental', 'Vendor Rental']);
                } elseif ($type === 'HT') {
                    return $query->whereIn('transaction_type', ['HT UV-82', 'HT 888s', 'HT UV-5R']);
                } elseif ($type === 'HabisPakai') {
                    return $query->whereIn('transaction_type', ['ATK', 'Obat']);
                } elseif ($type === 'Merchandise') {
                    return $query->whereIn('transaction_type', ['Merchandise', 'Sale']);
                }
                return $query->where('transaction_type', $type);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.items.index', compact('items', 'counts'));
    }
    
    public function create()
    {
        $categories = Category::all();
        return view('admin.items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'item_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'stock_quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            // 🌟 UPDATE VALIDASI 6 KATEGORI BARU 🌟
            'transaction_type' => 'required|in:Peralatan,HT UV-82,HT 888s,HT UV-5R,ATK,Obat,Merchandise,Internal Rental,Vendor Rental,Sale',
            'requires_mou' => 'required|boolean',
        ]);

        $path = $request->file('item_photo')->store('items', 'public');

        $validated['item_photo'] = $path;
        $validated['condition_status'] = 'Good';

        Item::create($validated);

        return redirect()->route('admin.items.index')->with('success', 'Barang berhasil ditambahkan ke gudang!');
    }

    public function edit(Item $item)
    {
        $categories = Category::all();
        return view('admin.items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'item_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'stock_quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            // 🌟 UPDATE VALIDASI 6 KATEGORI BARU 🌟
            'transaction_type' => 'required|in:Peralatan,HT UV-82,HT 888s,HT UV-5R,ATK,Obat,Merchandise,Internal Rental,Vendor Rental,Sale',
            'requires_mou' => 'required|boolean',
            'condition_status' => 'nullable|string',
        ]);

        if ($request->hasFile('item_photo')) {
            if ($item->item_photo) {
                Storage::disk('public')->delete($item->item_photo);
            }
            $validated['item_photo'] = $request->file('item_photo')->store('items', 'public');
        }

        $item->update($validated);

        return redirect()->route('admin.items.index')->with('success', 'Data barang berhasil diperbarui!');
    }

    public function destroy(Item $item)
    {
        if ($item->item_photo) {
            Storage::disk('public')->delete($item->item_photo);
        }
        $item->delete();
        return back()->with('success', 'Barang dihapus dari sistem.');
    }

    // =================================================================
    // 📝 ORDER MANAGEMENT & STATUS LOGIC
    // =================================================================

    public function orders(Request $request)
    {
        $search = $request->input('search');

        $orders = Order::with(['user', 'orderItems.item'])
            ->when($search, function ($query, $search) {
                return $query->where('order_number', 'like', "%{$search}%")
                             ->orWhereHas('user', function ($q) use ($search) {
                                 $q->where('name', 'like', "%{$search}%");
                             });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
            'mou_number' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'kwitansi_number' => 'nullable|string|max:255',
            'ba_number' => 'nullable|string|max:255',
            'ba_date' => 'nullable|string|max:255',
            'ba_due_date' => 'nullable|string|max:255',
            'ba_description' => 'nullable|string',
            'ba_total_fine' => 'nullable|numeric',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Daftar status yang menyebabkan barang KEMBALI KE GUDANG (Restock)
        $restockStatuses = ['Returned', 'Rejected', 'Cancelled', 'Resolved (Fine Paid)'];

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $order->update([
                'status' => $newStatus,
                'mou_number' => $request->mou_number,
                'invoice_number' => $request->invoice_number,
                'kwitansi_number' => $request->kwitansi_number,
                'ba_number' => $request->ba_number,
                'ba_date' => $request->ba_date,
                'ba_due_date' => $request->ba_due_date,
                'ba_description' => $request->ba_description,
                'ba_total_fine' => $request->ba_total_fine,
            ]);

            // 🌟 LOGIKA AUTO-RESTOCK (SESUAIKAN DENGAN BARANG HABIS PAKAI) 🌟
            if (in_array($newStatus, $restockStatuses) && !in_array($oldStatus, $restockStatuses)) {
                foreach ($order->orderItems as $detail) {
                    $item = \App\Models\Item::find($detail->item_id);
                    if ($item) {
                        // Jangan nambah stok jika tipenya Beli Putus / Habis Pakai dan statusnya Selesai
                        if (in_array($item->transaction_type, ['Sale', 'Merchandise', 'ATK', 'Obat']) && in_array($newStatus, ['Returned', 'Resolved (Fine Paid)'])) {
                            continue;
                        }
                        $item->increment('stock_quantity', $detail->quantity);
                    }
                }
            }
            
            // Tarik kembali stok dari gudang (Kalau batal Restock / status dimundurkan Admin)
            if (in_array($oldStatus, $restockStatuses) && !in_array($newStatus, $restockStatuses)) {
                foreach ($order->orderItems as $detail) {
                    $item = \App\Models\Item::lockForUpdate()->find($detail->item_id);
                    if ($item) {
                        if (in_array($item->transaction_type, ['Sale', 'Merchandise', 'ATK', 'Obat']) && in_array($oldStatus, ['Returned', 'Resolved (Fine Paid)'])) {
                            continue; 
                        }

                        if ($item->stock_quantity < $detail->quantity) {
                            \Illuminate\Support\Facades\DB::rollBack();
                            return back()->with('error', "Gagal mengubah status! Stok '{$item->name}' sudah dipinjam orang lain dan tidak cukup untuk ditarik kembali.");
                        }
                        $item->decrement('stock_quantity', $detail->quantity);
                    }
                }
            }

            // 🌟 TEMBAK NOTIFIKASINYA KE USER DI SINI 🌟
            if ($oldStatus !== $newStatus) {
                $order->user->notify(new OrderStatusUpdated($order));
            }

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', 'Status & Dokumen berhasil diperbarui!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function exportExcel() 
    {
        return Excel::download(new OrdersExport, 'inventory-report.xlsx');
    }

    // ==========================================================
    // 🗑️ FITUR HARD DELETE (HAPUS SPAM & BERSIHKAN MEMORI)
    // ==========================================================
    public function destroyOrder($id)
    {
        $order = Order::with('orderItems.item')->findOrFail($id);

        // 1. HAPUS FILE FISIK DI SERVER
        $filesToDelete = [
            $order->signed_mou,
            $order->payment_receipt,
            $order->signed_kwitansi,
            $order->signed_ba_file,
            $order->return_drive_link 
        ];

        foreach ($filesToDelete as $file) {
            if (!empty($file) && Storage::disk('public')->exists($file)) {
                Storage::disk('public')->delete($file);
            }
        }

        // 2. KEMBALIKAN STOK GUDANG JIKA BARANG HABIS PAKAI / BELI PUTUS (KECUALI UDAH DI REJECT/CANCEL)
        if (in_array($order->order_type, ['Sale', 'Merchandise', 'ATK', 'Obat']) && !in_array($order->status, ['Rejected', 'Cancelled'])) {
            foreach ($order->orderItems as $detail) {
                if($detail->item) {
                    $detail->item->increment('stock_quantity', $detail->quantity);
                }
            }
        }

        // 3. HAPUS DATA DARI DATABASE
        $order->orderItems()->delete();
        $order->delete(); 

        return back()->with('success', '🔥 Transaksi SPAM dan file dokumennya berhasil dimusnahkan dari server!');
    }
}