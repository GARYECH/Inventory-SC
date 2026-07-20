<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Category; // 🌟 WAJIB DITAMBAHKAN
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

        // 🌟 UPDATE: Menambahkan 'with('category')' agar server tidak berat (N+1 Query)
        $items = Item::with('category')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('admin.items.index', compact('items'));
    }

    public function create()
    {
        // 🌟 UPDATE: Ambil data kategori untuk dilempar ke dropdown form
        $categories = Category::all();
        return view('admin.items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // 🌟 UPDATE: Validasi ketat untuk kolom-kolom baru
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id', // Kategori harus ada di database
            'description' => 'required|string',
            'item_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'stock_quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'transaction_type' => 'required|in:Internal Rental,Vendor Rental,Sale', // 3 Jalur Navigasi
            'requires_mou' => 'required|boolean',
        ]);

        $path = $request->file('item_photo')->store('items', 'public');

        // Memasukkan file path dan default condition ke dalam array $validated
        $validated['item_photo'] = $path;
        $validated['condition_status'] = 'Good';

        Item::create($validated);

        return redirect()->route('admin.items.index')->with('success', 'Barang berhasil ditambahkan ke gudang!');
    }

    public function edit(Item $item)
    {
        // 🌟 UPDATE: Ambil kategori untuk dropdown saat edit
        $categories = Category::all();
        return view('admin.items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        // 🌟 UPDATE: Validasi untuk kolom-kolom baru
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
    // 📝 ORDER MANAGEMENT (KUITANSI)
    // =================================================================

    public function orders(Request $request)
    {
        $search = $request->input('search');

        // 🚨 CRITICAL FIX: Mengubah 'item' menjadi 'orderItems.item' karena sistem Keranjang
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
        // 🚨 CRITICAL FIX: Menghapus sementara logika '$order->item->increment()' lama
        // Karena sekarang isi order berbentuk array (banyak barang), pengembalian stok
        // harus di-looping (akan kita sempurnakan nanti saat masuk Phase Order).
        
        $request->validate([
            'status' => 'required|string'
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', "Status Kuitansi #{$order->order_number} berhasil diubah menjadi {$request->status}!");
    }

    public function exportExcel() 
    {
        return Excel::download(new OrdersExport, 'inventory-report.xlsx');
    }}
