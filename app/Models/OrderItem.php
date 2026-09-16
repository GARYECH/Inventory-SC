<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'subtotal_price' => 'integer',
        'size_additional_price' => 'integer',
        'color_number' => 'string',
    ];

    public function order()
    {
        return $this->belongsTo(
            Order::class
        );
    }

    public function item()
    {
        return $this->belongsTo(
            Item::class
        );
    }

    public function sizeBreakdowns()
    {
        return $this->hasMany(
            OrderItemSize::class
        )->orderByRaw(
            "CASE
                WHEN size = 'S' THEN 1
                WHEN size = 'M' THEN 2
                WHEN size = 'L' THEN 3
                WHEN size = 'XL' THEN 4
                WHEN size = '2XL' THEN 5
                WHEN size = '3XL' THEN 6
                WHEN size = '4XL' THEN 7
                WHEN size = '5XL' THEN 8
                ELSE 99
            END"
        );
    }

    public function isBaju(): bool
    {
        return $this->item &&
            $this->item->transaction_type === 'Merchandise' &&
            $this->item->subcategory === 'Baju';
    }
}