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

    // 1. Relasi ke Pemilik Kuitansi (Mahasiswa)
    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    // 2. Relasi Langsung ke Item (SANGAT PENTING untuk pivot size & design_link)
    public function items()
    {
        return $this->belongsToMany(Item::class, 'order_items')
                    ->withPivot('quantity', 'size', 'design_link', 'subtotal_price') 
                    ->withTimestamps();
    }

    // 3. Relasi ke model OrderItem (Jika tetap butuh akses ke Model Pivot-nya secara langsung)
    public function orderItems() 
    {
        return $this->hasMany(OrderItem::class);
    }

    // --- HELPER SAKTI ---

    // Fungsi otomatis untuk menghitung Total Harga dari seluruh isi keranjang
    public function getTotalPriceAttribute()
    {
        return $this->orderItems->sum('subtotal_price');
    }

    // Fungsi pintar untuk mengecek apakah transaksi ini mewajibkan MoU
    public function requiresMou()
    {
        // 1. Cek dari 4 Macro Category yang baru (Peralatan & HT biasanya wajib MoU)
        if (in_array($this->order_type, ['Peralatan', 'Handy Talkie'])) {
            return true;
        }

        // 2. Jika tipe Merchandise/Habis Pakai, loop isi keranjangnya
        // Baju dan ID Card dari kategori Merchandise wajib MoU
        foreach ($this->items as $item) { 
            if (in_array($item->category, ['Baju', 'ID Card']) || $item->requires_mou) {
                return true;
            }
        }

        return false;
    }
}