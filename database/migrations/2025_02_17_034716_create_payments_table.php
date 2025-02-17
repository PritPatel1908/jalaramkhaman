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
            $table->decimal('price', 8, 2)->nullable();
            $table->tinyInteger('status')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->tinyInteger('payment_type')->nullable();
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
