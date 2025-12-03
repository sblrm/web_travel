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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique(); // Format: BK-YYYYMMDD-XXXX
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('destination_id')->constrained()->cascadeOnDelete();
            $table->date('visit_date');
            $table->integer('quantity')->default(1); // Number of tickets
            $table->integer('unit_price'); // Price per ticket at booking time
            $table->integer('total_amount'); // Total amount to pay
            $table->string('visitor_name');
            $table->string('visitor_email');
            $table->string('visitor_phone');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'awaiting_payment', 'paid', 'verified', 'completed', 'cancelled', 'expired'])->default('pending');
            $table->timestamp('expires_at')->nullable(); // Payment deadline
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('visit_date');
            $table->index('status');
            $table->index('booking_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
