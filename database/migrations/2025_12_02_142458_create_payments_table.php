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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_method_id')->constrained();
            $table->string('payment_code')->unique(); // Format: PAY-YYYYMMDD-XXXX
            $table->integer('amount');
            $table->string('account_holder_name')->nullable(); // Nama pengirim
            $table->string('transfer_from')->nullable(); // Bank/ewallet pengirim
            $table->string('proof_image')->nullable(); // Bukti transfer path
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'uploaded', 'verified', 'rejected'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'status']);
            $table->index('status');
            $table->index('payment_code');
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
