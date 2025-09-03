<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'good_id',
        'quantity',
        'good_option_id',
        'base_price',
        'option_price',
        'total_price',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'base_price' => 0,
        'option_price' => 0,
        'total_price' => 0
    ];

    public function good(): BelongsTo
    {
        return $this->belongsTo(Good::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function goodOption(): BelongsTo
    {
        return $this->belongsTo(GoodOption::class, 'good_option_id');
    }

}
