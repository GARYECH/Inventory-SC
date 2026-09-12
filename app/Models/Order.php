<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_sop_accepted' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'order_items')
            ->withPivot(
                'quantity',
                'size',
                'design_link',
                'subtotal_price'
            )
            ->withTimestamps();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getTotalPriceAttribute()
    {
        return $this->orderItems->sum('subtotal_price');
    }

    public function requiresMou()
    {
        if (
            in_array($this->order_type, [
                'Peralatan',
                'Handy Talkie'
            ])
        ) {
            return true;
        }

        foreach ($this->items as $item) {
            if (
                in_array($item->transaction_type, [
                    'Peralatan',
                    'HT UV-82',
                    'HT 888s',
                    'HT UV-5R',
                    'Internal Rental',
                    'Vendor Rental'
                ])
            ) {
                return true;
            }

            if (
                $item->transaction_type === 'Merchandise' &&
                in_array($item->subcategory, [
                    'Baju',
                    'ID Card'
                ])
            ) {
                return true;
            }

            if ($item->requires_mou) {
                return true;
            }
        }

        return false;
    }
}