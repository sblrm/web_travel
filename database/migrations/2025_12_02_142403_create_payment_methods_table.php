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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Bank Transfer, E-Wallet, Cash
            $table->string('type'); // bank_transfer, e_wallet, cash
            $table->string('code')->unique(); // BCA, MANDIRI, GOPAY, etc.
            $table->text('account_number')->nullable(); // Rekening number
            $table->string('account_name')->nullable(); // Account holder name
            $table->text('instructions')->nullable(); // Payment instructions
            $table->string('icon')->nullable(); // Icon/logo path
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
