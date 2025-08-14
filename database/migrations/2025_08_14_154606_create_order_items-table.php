<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->foreignId('good_id');
            $table->integer('quantity');
            $table->foreignId('good_option_id')
                ->nullable()->constrained('good_options');
            $table->decimal('base_price', 7, 2)->default(0);
            $table->decimal('option_price', 7, 2)->default(0);
            $table->decimal('total_price', 7, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
