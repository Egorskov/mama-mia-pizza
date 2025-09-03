<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tymon\JWTAuth\Providers\Auth\Illuminate;

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

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'good_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(GoodOption::class, 'good_id');
    }

    public static function getAllGoods(): \Illuminate\Database\Eloquent\Collection
    {
        return self::all();
    }

    public static function createGood(array $data): Good
    {
        return self::create($data);
    }

    public function updateGood($data): bool
    {
        return $this->update($data);
    }

    public function deleteGood(): bool
    {
        return $this->delete();
    }

    public static function validateUpdateGood($data): array
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
