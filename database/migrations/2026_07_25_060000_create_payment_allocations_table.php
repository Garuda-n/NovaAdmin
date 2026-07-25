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
        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();

            // References
            $table->foreignId('sales_payment_id')
                ->index()
                ->constrained('sales_payments')
                ->restrictOnDelete();

            $table->foreignId('customer_receivable_id')
                ->index()
                ->constrained('customer_receivables')
                ->restrictOnDelete();

            // Allocation Information
            $table->decimal('allocated_amount', 18, 2)->default(0);
            $table->date('allocation_date')->index();

            // Allocation Source: 1 = Manual Allocation, 2 = Auto FIFO Allocation, 3 = Adjustment
            $table->tinyInteger('allocation_type')
                ->index()
                ->comment('1 = Manual Allocation, 2 = Auto FIFO Allocation, 3 = Adjustment');

            // Remarks
            $table->text('remarks')->nullable();

            // Audit Fields
            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Composite Index with explicit custom name under 64-character MySQL limit
            $table->index(
                ['sales_payment_id', 'customer_receivable_id'],
                'idx_allocations_payment_receivable'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_allocations');
    }
};
