<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_sop_accepted' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function orderItems()
    {
        return $this->hasMany(
            OrderItem::class
        );
    }

    public function items()
    {
        return $this->belongsToMany(
            Item::class,
            'order_items'
        )
        ->withPivot(
            'quantity',
            'size',
            'design_link',
            'size_additional_price',
            'subtotal_price'
        )
        ->withTimestamps();
    }

    public function mouDocuments()
    {
        return $this->hasMany(
            OrderMouDocument::class
        );
    }

    public function getTotalPriceAttribute(): int
    {
        return (int) $this->orderItems()
            ->sum('subtotal_price');
    }

    public function requiresMou(): bool
    {
        return $this->mouDocuments()->exists();
    }
}