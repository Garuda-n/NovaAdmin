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
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();

            // Foreign Key Relations
            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete();

            $table->foreignId('branch_id')
                ->index()
                ->constrained('branches')
                ->restrictOnDelete();

            $table->foreignId('counter_id')
                ->nullable()
                ->constrained('counters')
                ->restrictOnDelete();

            $table->foreignId('sales_id')
                ->index()
                ->constrained('sales')
                ->restrictOnDelete();

            $table->foreignId('customer_id')
                ->index()
                ->constrained('customers')
                ->restrictOnDelete();

            $table->foreignId('sales_person_id')
                ->index()
                ->constrained('users')
                ->restrictOnDelete();

            // Document Numbering
            $table->unsignedBigInteger('return_no')->unique();
            $table->string('return_no_display', 100)->unique();
            $table->date('return_date')->index();
            $table->date('business_date')->index();

            // GST Configuration
            $table->tinyInteger('gst_type')->comment('1 = CGST + SGST, 2 = IGST');

            // Calculation Fields
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('item_discount', 18, 2)->default(0);
            $table->decimal('invoice_discount', 18, 2)->default(0);
            $table->decimal('cgst_amount', 18, 2)->default(0);
            $table->decimal('sgst_amount', 18, 2)->default(0);
            $table->decimal('igst_amount', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('round_off', 18, 2)->default(0);
            $table->decimal('grand_total', 18, 2)->default(0);

            // Status: 1 = Completed, 2 = Cancelled
            $table->tinyInteger('status')
                ->default(1)
                ->index()
                ->comment('1 = Completed, 2 = Cancelled');

            // Remarks
            $table->text('remarks')->nullable();

            // Audit Stamp Columns
            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Composite Indexes for Reporting
            $table->index(['company_id', 'branch_id']);
            $table->index(['branch_id', 'return_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_returns');
    }
};
