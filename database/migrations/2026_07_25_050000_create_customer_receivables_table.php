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
        Schema::create('customer_receivables', function (Blueprint $table) {
            $table->id();

            // Foreign Keys (One-to-One with Credit Sales Invoices)
            $table->foreignId('sales_id')
                ->unique()
                ->constrained('sales')
                ->restrictOnDelete();

            $table->foreignId('customer_id')
                ->index()
                ->constrained('customers')
                ->restrictOnDelete();

            // Invoice Snapshots
            $table->date('invoice_date');
            $table->date('due_date')->index();

            // Receivable Position Running Amounts
            $table->decimal('original_amount', 18, 2)->default(0);
            $table->decimal('paid_amount', 18, 2)->default(0);
            $table->decimal('balance_amount', 18, 2)->default(0);

            // Status: 1 = Pending, 2 = Partially Paid, 3 = Paid, 4 = Cancelled
            $table->tinyInteger('status')
                ->default(1)
                ->index()
                ->comment('1 = Pending, 2 = Partially Paid, 3 = Paid, 4 = Cancelled');

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

            // Composite Indexes for Receivables Reporting & Aging Analysis
            $table->index(['customer_id', 'status']);
            $table->index(['customer_id', 'due_date']);
            $table->index(['status', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_receivables');
    }
};
