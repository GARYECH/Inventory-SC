<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

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
        'condition_status',
    ];

    public function category()
    {
        return $this->belongsTo(
            Category::class
        );
    }

    public function orderItems()
    {
        return $this->hasMany(
            OrderItem::class
        );
    }

    public function getAvailableStockForDate($date)
    {
        if (!in_array($this->transaction_type, [
            'Peralatan',
            'HT UV-82',
            'HT 888s',
            'HT UV-5R',
            'Internal Rental',
            'Vendor Rental',
        ])) {
            return $this->stock_quantity;
        }

        $booked = $this->orderItems()
            ->whereHas('order', function ($query) use ($date) {
                $query
                    ->whereNotIn('status', [
                        'Returned',
                        'Resolved (Fine Paid)',
                        'Rejected',
                        'Cancelled',
                    ])
                    ->whereDate(
                        'start_date',
                        '<=',
                        $date
                    )
                    ->whereDate(
                        'end_date',
                        '>=',
                        $date
                    );
            })
            ->sum('quantity');

        return max(
            0,
            $this->stock_quantity - $booked
        );
    }
}