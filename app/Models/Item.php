<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
protected $fillable = ['name', 'description', 'item_photo', 'price', 'stock_quantity', 'condition_status'];

public function orders() {
    return $this->hasMany(Order::class);
}
public function getAvailableStockForDate($date)
{
    $booked = $this->orders()
        ->whereIn('status', ['Pending', 'Approved', 'Borrowed'])
        ->where('start_date', '<=', $date)
        ->where('end_date', '>=', $date)
        ->sum('quantity');

    return $this->stock_quantity - $booked;
}
    //
}
