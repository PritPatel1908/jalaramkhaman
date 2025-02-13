<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('business_type_product_prices', function (Blueprint $table) {
            $table->id();
            $table->decimal('price', 8, 2)->nullable();
            $table->decimal('qty', 8, 2)->nullable();
            $table->tinyInteger('qty_in')->nullable();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_type_product_prices');
    }
};
