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
        Schema::create('recurring_orders', function (Blueprint $table) {
            $table->id();
            $table->enum('order_period', ['daily', 'weekly', 'monthly'])->nullable();
            $table->dateTime('last_created_date')->nullable();
            $table->enum('payment_cycle', ['daily', 'weekly', 'monthly'])->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_orders');
    }
};
