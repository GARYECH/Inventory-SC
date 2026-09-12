<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AdminItemController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');

        $counts = [
            'total' => Item::count(),
            'peralatan' => Item::whereIn('transaction_type', [
                'Peralatan',
                'Internal Rental',
                'Vendor Rental'
            ])->count(),
            'ht' => Item::whereIn('transaction_type', [
                'HT UV-82',
                'HT 888s',
                'HT UV-5R'
            ])->count(),
            'habispakai' => Item::whereIn('transaction_type', [
                'ATK',
                'Obat'
            ])->count(),
            'merchandise' => Item::where('transaction_type', 'Merchandise')->count(),
        ];

        $items = Item::with('category')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('subcategory', 'like', "%{$search}%");
                });
            })
            ->when($type, function ($query, $type) {
                if ($type === 'Peralatan') {
                    return $query->whereIn('transaction_type', [
                        'Peralatan',
                        'Internal Rental',
                        'Vendor Rental'
                    ]);
                }

                if ($type === 'HT') {
                    return $query->whereIn('transaction_type', [
                        'HT UV-82',
                        'HT 888s',
                        'HT UV-5R'
                    ]);
                }

                if ($type === 'HabisPakai') {
                    return $query->whereIn('transaction_type', [
                        'ATK',
                        'Obat'
                    ]);
                }

                if ($type === 'Merchandise') {
                    return $query->where('transaction_type', 'Merchandise');
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
            'transaction_type' => 'required|in:Peralatan,HT UV-82,HT 888s,HT UV-5R,ATK,Obat,Merchandise,Internal Rental,Vendor Rental',
            'subcategory' => 'nullable|string|max:255',
            'requires_mou' => 'required|boolean',
        ]);

        $validated['item_photo'] = $request->file('item_photo')->store('items', 'public');
        $validated['condition_status'] = 'Good';

        Item::create($validated);

        return redirect()
            ->route('admin.items.index')
            ->with('success', 'Barang berhasil ditambahkan ke gudang!');
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
            'transaction_type' => 'required|in:Peralatan,HT UV-82,HT 888s,HT UV-5R,ATK,Obat,Merchandise,Internal Rental,Vendor Rental',
            'subcategory' => 'nullable|string|max:255',
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

        return redirect()
            ->route('admin.items.index')
            ->with('success', 'Data barang berhasil diperbarui!');
    }

    public function destroy(Item $item)
    {
        if ($item->item_photo) {
            Storage::disk('public')->delete($item->item_photo);
        }

        $item->delete();

        return back()->with('success', 'Barang dihapus dari sistem.');
    }

    public function orders(Request $request)
    {
        $search = $request->input('search');

        $orders = Order::with(['user', 'orderItems.item'])
            ->when($search, function ($query, $search) {
                $query->where('order_number', 'like', "%{$search}%")
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

        $restockStatuses = [
            'Returned',
            'Rejected',
            'Cancelled',
            'Resolved (Fine Paid)'
        ];

        DB::beginTransaction();

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

            if (
                in_array($newStatus, $restockStatuses) &&
                !in_array($oldStatus, $restockStatuses)
            ) {
                foreach ($order->orderItems as $detail) {
                    $item = Item::find($detail->item_id);

                    if (!$item) {
                        continue;
                    }

                    if (
                        in_array($item->transaction_type, [
                            'Merchandise',
                            'ATK',
                            'Obat'
                        ]) &&
                        in_array($newStatus, [
                            'Returned',
                            'Resolved (Fine Paid)'
                        ])
                    ) {
                        continue;
                    }

                    $item->increment(
                        'stock_quantity',
                        $detail->quantity
                    );
                }
            }

            if (
                in_array($oldStatus, $restockStatuses) &&
                !in_array($newStatus, $restockStatuses)
            ) {
                foreach ($order->orderItems as $detail) {
                    $item = Item::lockForUpdate()->find($detail->item_id);

                    if (!$item) {
                        continue;
                    }

                    if (
                        in_array($item->transaction_type, [
                            'Merchandise',
                            'ATK',
                            'Obat'
                        ]) &&
                        in_array($oldStatus, [
                            'Returned',
                            'Resolved (Fine Paid)'
                        ])
                    ) {
                        continue;
                    }

                    if ($item->stock_quantity < $detail->quantity) {
                        DB::rollBack();

                        return back()->with(
                            'error',
                            "Gagal mengubah status! Stok '{$item->name}' sudah tidak cukup untuk ditarik kembali."
                        );
                    }

                    $item->decrement(
                        'stock_quantity',
                        $detail->quantity
                    );
                }
            }

            if ($oldStatus !== $newStatus) {
                $order->user->notify(
                    new OrderStatusUpdated($order)
                );
            }

            DB::commit();

            return back()->with(
                'success',
                'Status & Dokumen berhasil diperbarui!'
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with(
                'error',
                'Terjadi kesalahan: ' . $e->getMessage()
            );
        }
    }

    public function exportExcel()
    {
        return Excel::download(
            new OrdersExport,
            'inventory-report.xlsx'
        );
    }

    public function destroyOrder($id)
    {
        $order = Order::with('orderItems.item')->findOrFail($id);

        $filesToDelete = [
            $order->signed_mou,
            $order->payment_receipt,
            $order->signed_kwitansi,
            $order->signed_ba_file,
            $order->return_drive_link
        ];

        foreach ($filesToDelete as $file) {
            if (
                !empty($file) &&
                Storage::disk('public')->exists($file)
            ) {
                Storage::disk('public')->delete($file);
            }
        }

        foreach ($order->orderItems as $detail) {
            if (!$detail->item) {
                continue;
            }

            if (
                in_array($detail->item->transaction_type, [
                    'Merchandise',
                    'ATK',
                    'Obat'
                ]) &&
                !in_array($order->status, [
                    'Rejected',
                    'Cancelled'
                ])
            ) {
                $detail->item->increment(
                    'stock_quantity',
                    $detail->quantity
                );
            }
        }

        $order->orderItems()->delete();
        $order->delete();

        return back()->with(
            'success',
            '🔥 Transaksi SPAM dan file dokumennya berhasil dimusnahkan dari server!'
        );
    }
}