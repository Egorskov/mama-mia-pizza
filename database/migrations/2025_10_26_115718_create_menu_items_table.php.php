<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('menu_items', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('category');     // pizza / drinks / snacks …
            $t->integer('size')->nullable(); // см, например 25 / 30 / 35
            $t->integer('popularity')->default(0); // счётчик заказов/просмотров
            $t->decimal('price', 8, 2);
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('menu_items');
    }
};
