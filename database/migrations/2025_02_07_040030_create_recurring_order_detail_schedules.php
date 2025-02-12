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
        Schema::create('recurring_order_detail_schedules', function (Blueprint $table) {
            $table->id();
            $table->decimal('qty', 8, 2)->nullable();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('recurring_order_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_order_details');
    }
};
