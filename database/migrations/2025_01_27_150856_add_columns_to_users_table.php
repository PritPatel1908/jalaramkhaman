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
        Schema::table('users', function (Blueprint $table) {
            // $table->string('code')->nullable();
            $table->string('fname')->nullable();
            $table->string('mname')->nullable();
            $table->string('lname')->nullable();
            $table->string('number')->nullable();
            $table->enum('user_type', ['admin', 'business', 'customer', 'operator'])->nullable();
            $table->boolean('is_locked')->nullable();
            $table->string('profile_pic')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->default('male')->nullable();
            $table->boolean('is_activate')->default(false);
            $table->tinyInteger('order_period')->nullable();
            $table->tinyInteger('payment_cycle')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('fname');
            $table->dropColumn('mname');
            $table->dropColumn('lname');
            $table->dropColumn('number');
            $table->dropColumn('user_type');
            $table->dropColumn('is_locked');
            $table->dropColumn('profile_pic');
            $table->dropColumn('dob');
            $table->dropColumn('gender');
            $table->dropColumn('is_activate');
        });
    }
};
