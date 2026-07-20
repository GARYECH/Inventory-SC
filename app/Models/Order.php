<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Tinggalkan $fillable yang panjang, gunakan $guarded agar lebih rapi dan dinamis.
    protected $guarded = ['id'];

    // Casts: Memberi tahu Laravel agar otomatis mengubah format data ini saat diambil dari database.
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_sop_accepted' => 'boolean',
    ];

    // 1. Relasi ke Pemilik Kuitansi (Mahasiswa)
    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    // 2. Relasi ke Rincian Barang (Cart Items) - INI PENGGANTI RELASI item() YANG LAMA
    public function orderItems() 
    {
        return $this->hasMany(OrderItem::class);
    }

    // --- HELPER SAKTI (Bonus Senior Engineer) ---

    // Fungsi otomatis untuk menghitung Total Harga dari seluruh isi keranjang
    public function getTotalPriceAttribute()
    {
        return $this->orderItems->sum('subtotal_price');
    }

    // Fungsi pintar untuk mengecek apakah kuitansi ini mewajibkan mahasiswa upload MoU
    public function requiresMou()
    {
        // Jika ini Sale (Beli), kamu bilang kemarin semua Buy wajib MoU
        if ($this->order_type === 'Sale') {
            return true;
        }

        // Jika ini Rental, cek apakah ada minimal 1 barang di keranjang yang butuh MoU
        foreach ($this->orderItems as $detail) {
            if ($detail->item->requires_mou) {
                return true;
            }
        }

        return false;
    }
}