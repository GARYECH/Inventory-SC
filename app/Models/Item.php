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
        'transaction_detail',
        'subcategory',
        'requires_mou',
        'description',
        'item_photo',
        'stock_quantity',
        'price',
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

    public function getTransactionLabelAttribute(): string
    {
        return $this->transaction_type;
    }

    public function getRequiresReturnAttribute(): bool
    {
        return in_array(
            $this->transaction_type,
            [
                'Peralatan',
                'Handy Talkie',
            ],
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | LEGACY DATE STOCK CHECK
    |--------------------------------------------------------------------------
    |
    | Dipertahankan supaya kode lama tidak error.
    |
    */

    public function getAvailableStockForDate($date)
    {
        if (!$this->requires_return) {
            return (int) $this->stock_quantity;
        }

        $date = Carbon\Carbon::parse($date)->toDateString();

        $booked = $this->orderItems()
            ->whereHas('order', function ($query) use ($date) {
                $query
                    ->whereNotIn(
                        'status',
                        [
                            'Returned',
                            'Returned (Damaged)',
                            'Resolved (Fine Paid)',
                            'Rejected',
                            'Cancelled',
                        ]
                    )
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
            (int) $this->stock_quantity - (int) $booked
        );
    }
}