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
        'return_submitted_at' => 'datetime',
        'ht_fee' => 'integer',
        'transport_fee' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | USER
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ORDER ITEMS
    |--------------------------------------------------------------------------
    */

    public function orderItems()
    {
        return $this->hasMany(
            OrderItem::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ITEMS
    |--------------------------------------------------------------------------
    */

    public function items()
    {
        return $this->belongsToMany(
            Item::class,
            'order_items'
        )
        ->withPivot(
            'quantity',
            'size',
            'color_number',
            'design_link',
            'size_additional_price',
            'subtotal_price'
        )
        ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | MOU DOCUMENTS
    |--------------------------------------------------------------------------
    */

    public function mouDocuments()
    {
        return $this->hasMany(
            OrderMouDocument::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TOTAL PRICE
    |--------------------------------------------------------------------------
    */

    public function getTotalPriceAttribute(): int
    {
        $itemTotal =
            (int) $this->orderItems()
                ->sum(
                    'subtotal_price'
                );

        $htFee =
            (int) (
                $this->attributes['ht_fee']
                ?? 0
            );

        $transportFee =
            (int) (
                $this->attributes['transport_fee']
                ?? 0
            );

        return
            $itemTotal
            +
            $htFee
            +
            $transportFee;
    }

    /*
    |--------------------------------------------------------------------------
    | TAX / GROSS-UP
    |--------------------------------------------------------------------------
    */

    public const TAX_RATE = 0.02;

    public function isTaxApplicable(): bool
    {
        return ($this->attributes['funding_source'] ?? null) === 'finance_uc'
            && in_array(
                $this->attributes['order_type'] ?? null,
                [
                    'Peralatan',
                    'Handy Talkie',
                    'Merchandise',
                ],
                true
            );
    }

    public function getTaxAmountAttribute(): int
    {
        if (!$this->isTaxApplicable()) {
            return 0;
        }

        // Gross-up PPh 2% so the base amount remains net of tax.
        $taxRate = self::TAX_RATE;
        $baseAmount = (int) $this->total_price;

        return (int) round(
            $baseAmount * $taxRate / (1 - $taxRate)
        );
    }

    public function getGrandTotalPriceAttribute(): int
    {
        return (int) $this->total_price + (int) $this->tax_amount;
    }

    /*
    |--------------------------------------------------------------------------
    | MOU CHECK
    |--------------------------------------------------------------------------
    */

    public function requiresMou(): bool
    {
        return $this->mouDocuments()
            ->exists();
    }
}
