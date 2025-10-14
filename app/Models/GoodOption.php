<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodOption extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'good_id',
        'type',
        'name',
        'price'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function good()
    {
        return $this->belongsTo(Good::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'good_option_id');
    }
}
