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
        Schema::create('sales_payments', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->foreignId('sales_id')
                ->index()
                ->constrained('sales')
                ->cascadeOnDelete();

            $table->foreignId('payment_mode_id')
                ->index()
                ->constrained('payment_modes')
                ->restrictOnDelete();

            // Payment Details
            $table->date('payment_date')->index();
            $table->decimal('amount', 18, 2);

            // Reference Information (UPI Txn ID, Card Approval, Cheque No, Bank Ref, etc.)
            $table->string('reference_no', 100)->nullable();

            // Remarks
            $table->text('remarks')->nullable();

            // Status: 1 = Completed, 2 = Cancelled
            $table->tinyInteger('status')
                ->default(1)
                ->index()
                ->comment('1 = Completed, 2 = Cancelled');

            // Cancellation Details
            $table->foreignId('cancelled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason', 100)->nullable();

            // Audit Fields
            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Composite Indexes for Performance & Financial Reporting
            $table->index(['sales_id', 'status']);
            $table->index(['payment_mode_id', 'payment_date']);
            $table->index(['payment_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_payments');
    }
};
