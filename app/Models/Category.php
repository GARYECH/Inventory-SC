<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = ['id'];

    // 1 Kategori punya BANYAK Barang
    public function items()
    {
        return $this->hasMany(Item::class);
    }
}