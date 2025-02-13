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
        Schema::create('recurring_order_schedules', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('order_period')->nullable();
            $table->dateTime('created_date')->nullable();
            $table->tinyInteger('payment_cycle')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->tinyInteger('status')->default(4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_order_schedules');
    }
};
