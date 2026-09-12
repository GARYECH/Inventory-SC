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

    public function getTransactionLabelAttribute()
    {
        if (in_array(
            $this->transaction_type,
            [
                'Peralatan',
                'Internal Rental',
                'Vendor Rental',
            ],
            true
        )) {
            return 'Peralatan';
        }

        if (in_array(
            $this->transaction_type,
            [
                'HT UV-82',
                'HT 888s',
                'HT UV-5R',
            ],
            true
        )) {
            return 'Handy Talkie';
        }

        if (in_array(
            $this->transaction_type,
            [
                'ATK',
                'Obat',
            ],
            true
        )) {
            return 'Habis Pakai';
        }

        if ($this->transaction_type === 'Merchandise') {
            return 'Merchandise';
        }

        return 'Peralatan';
    }

    public function getRequiresReturnAttribute()
    {
        return in_array(
            $this->transaction_type,
            [
                'Peralatan',
                'Internal Rental',
                'Vendor Rental',
                'HT UV-82',
                'HT 888s',
                'HT UV-5R',
            ],
            true
        );
    }

    public function getAvailableStockForDate($date)
    {
        if (!$this->requires_return) {
            return $this->stock_quantity;
        }

        $booked = $this->orderItems()
            ->whereHas(
                'order',
                function ($query) use ($date) {
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
                }
            )
            ->sum('quantity');

        return max(
            0,
            $this->stock_quantity - $booked
        );
    }
}