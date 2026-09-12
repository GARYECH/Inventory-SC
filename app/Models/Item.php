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

        /*
         * Transaction Type hanya mempunyai 4 nilai:
         *
         * - Peralatan
         * - Handy Talkie
         * - Habis Pakai
         * - Merchandise
         */
        'transaction_type',

        /*
         * Detail tambahan dari Transaction Type.
         *
         * Peralatan:
         * - Internal Rental
         * - Vendor Rental
         *
         * Handy Talkie:
         * - HT UV-82
         * - HT 888s
         * - HT UV-5R
         */
        'transaction_detail',

        /*
         * Khusus Merchandise:
         * - Baju
         * - ID Card
         * - Lainnya
         */
        'subcategory',

        'requires_mou',
        'description',
        'item_photo',
        'stock_quantity',
        'price',
        'condition_status',
    ];


    /*
    |--------------------------------------------------------------------------
    | CATEGORY
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo(
            Category::class
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
    | TRANSACTION TYPE LABEL
    |--------------------------------------------------------------------------
    |
    | Transaction Type sudah langsung menyimpan salah satu dari 4
    | nilai final. Tidak perlu mapping HT / ATK / Obat lagi.
    |
    */

    public function getTransactionLabelAttribute(): string
    {
        return $this->transaction_type;
    }


    /*
    |--------------------------------------------------------------------------
    | RETURNABLE / RENTAL
    |--------------------------------------------------------------------------
    |
    | Barang yang dikembalikan:
    *
    * - Peralatan
    * - Handy Talkie
    *
    * Barang Habis Pakai dan Merchandise tidak perlu dikembalikan.
    |
    */

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
    | RENTAL / STOCK CHECK
    |--------------------------------------------------------------------------
    |
    | Untuk barang returnable, stok tersedia harus memperhitungkan
    | booking pada tanggal yang dipilih.
    |
    */

    public function getAvailableStockForDate($date)
    {
        /*
         * Habis Pakai dan Merchandise tidak memakai sistem
         * booking stok berdasarkan tanggal.
         */
        if (!$this->requires_return) {

            return $this->stock_quantity;

        }


        /*
         * Hitung jumlah barang yang sedang dipinjam.
         */
        $booked =
            $this->orderItems()
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
                ->sum(
                    'quantity'
                );


        return max(
            0,
            $this->stock_quantity -
            $booked
        );
    }
}