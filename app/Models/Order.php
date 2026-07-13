<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['order_number', 'user_id', 'item_id', 'quantity', 'total_price', 'status', 'start_date', 'end_date', 'admin_notes'];

public function user() {
    return $this->belongsTo(User::class);
}

public function item() {
    return $this->belongsTo(Item::class);
}
    //
}
