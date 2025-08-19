<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'good_id',
        'quantity',
        'good_option_id',
    ];

    protected $attributes = [
        'base_price' => 0,
        'option_price' => 0,
        'total_price' => 0
    ];

    protected static function booted()
    {
        static::creating(function ($item) {
            $item->base_price = $item->good->price;
            $item->option_price = $item->goodOption ? $item->goodOption->price : 0;
            $item->total_price = ($item->base_price + $item->option_price) * $item->quantity;
        });
    }

    public function good()
    {
        return $this->belongsTo(Good::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function goodOption()
    {
        return $this->belongsTo(GoodOption::class, 'good_option_id');
    }

}
