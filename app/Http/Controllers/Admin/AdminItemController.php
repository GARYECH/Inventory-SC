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

class AdminItemController extends Controller
{
    // =================================================================
    // 📦 ITEM MANAGEMENT (GUDANG)
    // =================================================================

   public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');

        $counts = [
            'total' => Item::count(),
            'internal' => Item::where('transaction_type', 'Internal Rental')->count(),
            'external' => Item::where('transaction_type', 'Vendor Rental')->count(),
            'merchandise' => Item::where('transaction_type', 'Sale')->count(),
        ];

        $items = Item::with('category')
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($type, function ($query, $type) {
                return $query->where('transaction_type', $type);
            })
            ->latest()
            ->paginate(8)
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
            'transaction_type' => 'required|in:Internal Rental,Vendor Rental,Sale',
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
            'transaction_type' => 'required|in:Internal Rental,Vendor Rental,Sale',
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
    // 📝 ORDER MANAGEMENT (KUITANSI & BERITA ACARA)
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
        // 🌟 TANGKAP SEMUA INPUTAN ADMIN TERMASUK BERITA ACARA
        $request->validate([
            'status' => 'required|string',
            'invoice_number' => 'nullable|string|max:255',
            'kwitansi_number' => 'nullable|string|max:255',
            // 👇 TAMBAHAN UNTUK BERITA ACARA 👇
            'ba_number' => 'nullable|string|max:255',
            'ba_date' => 'nullable|string|max:255',
            'ba_due_date' => 'nullable|string|max:255',
            'ba_description' => 'nullable|string',
            'ba_total_fine' => 'nullable|numeric',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // 💡 Catatan: "Resolved (Fine Paid)" dimasukkan ke dalam daftar restock 
        // asumsinya saat mahasiswa melunasi denda ganti rugi, asetnya akan dibeli ulang
        // dan kembali tersedia di gudang SC.
        $restockStatuses = ['Returned', 'Rejected', 'Cancelled', 'Resolved (Fine Paid)'];

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // 🌟 SIMPAN DATA STATUS DAN SEMUA NOMOR SURAT KE DATABASE
            $order->update([
                'status' => $newStatus,
                'invoice_number' => $request->invoice_number,
                'kwitansi_number' => $request->kwitansi_number,
                // 👇 SIMPAN DATA BERITA ACARA 👇
                'ba_number' => $request->ba_number,
                'ba_date' => $request->ba_date,
                'ba_due_date' => $request->ba_due_date,
                'ba_description' => $request->ba_description,
                'ba_total_fine' => $request->ba_total_fine,
            ]);

            // Auto Restock (Balik ke gudang)
            if (in_array($newStatus, $restockStatuses) && !in_array($oldStatus, $restockStatuses)) {
                foreach ($order->orderItems as $detail) {
                    $item = \App\Models\Item::find($detail->item_id);
                    if ($item) {
                        $item->increment('stock_quantity', $detail->quantity);
                    }
                }
            }
            
            // Tarik kembali stok dari gudang (Kalau batal Restock)
            if (in_array($oldStatus, $restockStatuses) && !in_array($newStatus, $restockStatuses)) {
                foreach ($order->orderItems as $detail) {
                    $item = \App\Models\Item::lockForUpdate()->find($detail->item_id);
                    if ($item) {
                        if ($item->stock_quantity < $detail->quantity) {
                            \Illuminate\Support\Facades\DB::rollBack();
                            return back()->with('error', "Gagal mengubah status! Stok '{$item->name}' sudah dipinjam orang lain dan tidak cukup untuk ditarik kembali.");
                        }
                        $item->decrement('stock_quantity', $detail->quantity);
                    }
                }
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
}