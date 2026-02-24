<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $table = 'menu_items';

    protected $fillable = [
        'name',
        'category',
        'size',
        'popularity',
        'price',
    ];

    /**
     * Пример аксессора: форматировать цену
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2, '.', ' ') . ' ₽';
    }

    /**
     * Пример скоупа: фильтрация по категории
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Пример скоупа: сортировка по популярности
     */
    public function scopePopular($query)
    {
        return $query->orderByDesc('popularity');
    }
}
