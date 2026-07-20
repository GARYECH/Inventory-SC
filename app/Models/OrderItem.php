<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // 1. Kembali ke Kuitansi Induk
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // 2. Menuju ke Barang di Gudang
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}