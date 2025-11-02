<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Композитный индекс: фильтрация по category, size + сортировка по popularity (DESC)
        DB::statement('CREATE INDEX IF NOT EXISTS idx_menu_category_size_popularity
                       ON menu_items (category, size, popularity DESC)');
    }
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS idx_menu_category_size_popularity');
    }
};
