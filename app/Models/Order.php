<?php

namespace App\Models;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Order extends Model
{
    protected $fillable = ['user_id','total','status','notes','order_no'];
    public function items() {
        return $this->hasMany(OrderItem::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
}
