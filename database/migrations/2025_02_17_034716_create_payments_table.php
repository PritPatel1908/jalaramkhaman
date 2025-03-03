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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('oderabel_type')->nullable();
            $table->integer('oderabel_id')->nullable();
            $table->decimal('total_amount', 8, 2)->nullable();
            $table->tinyInteger('payment_status')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->dateTime('payment_complate_date')->nullable();
            $table->tinyInteger('payment_type')->nullable();
            $table->tinyInteger('payment_via')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
