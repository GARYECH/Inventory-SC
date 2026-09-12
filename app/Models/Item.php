<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'transaction_type',
        'subcategory',
        'requires_mou',
        'description',
        'item_photo',
        'price',
        'stock_quantity',
        'condition_status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getAvailableStockForDate($date)
    {
        $booked = $this->orderItems()
            ->whereHas('order', function ($query) use ($date) {
                $query
                    ->whereIn('status', [
                        'Pending',
                        'Approved',
                        'Waiting for MoU',
                        'Paid',
                        'Handed Over'
                    ])
                    ->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date);
            })
            ->sum('quantity');

        return $this->stock_quantity - $booked;
    }
}