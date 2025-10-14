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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('user_address_id')
                ->default(1) -> constrained('user_addresses');
            $table->dateTime('delivery_time')->nullable();
            $table->enum('delivery_status',
                ['pending', 'delivered',
                    'processing', 'shipping', 'cancelled'])
                ->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
