<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Providers\Auth\Illuminate;

class Good extends Model
{
    use HasFactory;

    public $timestamps = false;
    /**
     * @var \Illuminate\Support\HigherOrderCollectionProxy|int|mixed
     */
    public mixed $views_count;

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
        return Cache::remember('goods', 3600, function () {
            return self::all();
        });
    }

    public static function createGood(array $data): Good
    {
        $good = self::create($data);
        self::invalidateCached();
        return $good;
    }

    public function updateGood($data): bool
    {
        $this->update($data);
        self::invalidateCached($this->id);
        return true;
    }

    public function deleteGood(): bool
    {
        $this->delete();
        self::invalidateCached($this->id);
        return true;
    }

    private static function invalidateCached($id = null): void
    {
        Cache::forget('goods:all');
        if ($id) {
            Cache::forget('goods:' . $id);
        }
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
