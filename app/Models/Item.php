<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    // 1. TAMBAHKAN FIELD BARU AGAR TIDAK ERROR 500
    protected $fillable = [
        'name', 
        'category_id', 
        'transaction_type', 
        'requires_mou', 
        'description', 
        'item_photo', 
        'price', 
        'stock_quantity', 
        'condition_status'
    ];

    // 2. RELASI KE KATEGORI
    public function category() 
    {
        return $this->belongsTo(Category::class);
    }

    // 3. RELASI KE KERANJANG (ORDER ITEMS)
    public function orderItems() 
    {
        return $this->hasMany(OrderItem::class);
    }

    // 4. PERBAIKAN LOGIKA STOK UNTUK SISTEM KERANJANG
    public function getAvailableStockForDate($date)
    {
        // Cari barang ini yang sedang di-booking di dalam orderItems
        $booked = $this->orderItems()
            ->whereHas('order', function ($query) use ($date) {
                // Jangan hitung order yang 'Returned', 'Cancelled', atau 'Rejected'
                $query->whereIn('status', ['Pending', 'Approved', 'Waiting for MoU', 'Paid', 'Handed Over'])
                      ->where('start_date', '<=', $date)
                      ->where('end_date', '>=', $date);
            })
            ->sum('quantity');

        return $this->stock_quantity - $booked;
    }
}