<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Good extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'price',
        'weight',
        'category'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'good_id');
    }

    public function options()
    {
        return $this->hasMany(GoodOption::class, 'good_id');
    }

    public static function getAllGoods()
    {
        return self::all();
    }

    public static function createGood(array $data)
    {
        return self::create($data);
    }

    public function updateGood($data): bool
    {
        return $this->update($data);
    }

    public function deleteGood(): bool
    {
        return self::delete();
    }

    public static function validateUpdateGood($data)
    {
        return $data ->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|min:0',
            'weight' => 'required|integer|min:0',
            'category' => 'required|in:pizza,drink',
        ]);
    }



}
