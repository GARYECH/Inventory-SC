<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemSize extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'size_additional_price' => 'integer',
        'subtotal_price' => 'integer',
    ];

    public function orderItem()
    {
        return $this->belongsTo(
            OrderItem::class
        );
    }
}