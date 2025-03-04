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
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('payment_type')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('upi_id_no')->nullable();
            $table->string('bank_detail')->nullable();
            $table->string('account_no')->nullable();
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_details');
    }
};
