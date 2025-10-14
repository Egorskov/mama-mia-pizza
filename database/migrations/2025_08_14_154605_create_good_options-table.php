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
        Schema::create('good_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('good_id');
            $table->enum('type',['dough', 'board']);
            $table->string('name');
            $table->decimal('price',7,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('good_options');
    }
};
