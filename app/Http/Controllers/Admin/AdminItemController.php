<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminItemController extends Controller
{
   public function index(Request $request)
{
    $search = $request->input('search');

    $items = Item::when($search, function ($query, $search) {
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
        return view('admin.items.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'item_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('item_photo')->store('items', 'public');

        Item::create([
            'name' => $request->name,
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'item_photo' => $path,
            'condition_status' => 'Good',
        ]);

        return redirect()->route('admin.items.index')->with('success', 'Item added successfully!');
    }

    public function edit(Item $item)
    {
        return view('admin.items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'item_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('item_photo')) {
            if ($item->item_photo) {
                Storage::disk('public')->delete($item->item_photo);
            }
            $item->item_photo = $request->file('item_photo')->store('items', 'public');
        }

        $item->update([
            'name' => $request->name,
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'condition_status' => $request->condition_status ?? $item->condition_status,
        ]);

        return redirect()->route('admin.items.index')->with('success', 'Item updated!');
    }

    public function destroy(Item $item)
    {
        if ($item->item_photo) {
            Storage::disk('public')->delete($item->item_photo);
        }
        $item->delete();
        return back()->with('success', 'Item deleted.');
    }

    // --- 🏛️ NEW APPROVAL LOGIC METHODS ---

   public function orders(Request $request)
{
    $search = $request->input('search');

    $orders = \App\Models\Order::with(['user', 'item'])
        ->when($search, function ($query, $search) {
            return $query->where('order_number', 'like', "%{$search}%")
                         ->orWhereHas('user', function ($q) use ($search) {
                             $q->where('name', 'like', "%{$search}%");
                         });
        })
        ->latest()
        ->paginate(10) // Tampilkan 10 pesanan per halaman
        ->withQueryString();

    return view('admin.orders.index', compact('orders'));
}

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:Approved,Borrowed,Returned,Cancelled'
        ]);

        // AUTOMATION: If marking as Returned, increment the item stock by the quantity borrowed
        if ($request->status === 'Returned' && $order->status !== 'Returned') {
            $order->item->increment('stock_quantity', $order->quantity);
        }
        
        // AUTOMATION: If you want stock to decrease immediately upon "Borrowed" (Optional)
        // Currently, our logic decreases stock during the Request (store) to prevent over-booking.

        $order->update(['status' => $request->status]);

        return back()->with('success', "Order #{$order->order_number} status updated to {$request->status}!");
    }

    public function exportExcel() 
{
    // Ini akan memanggil class OrdersExport yang kita buat tadi
    return Excel::download(new OrdersExport, 'inventory-report.xlsx');
}
}