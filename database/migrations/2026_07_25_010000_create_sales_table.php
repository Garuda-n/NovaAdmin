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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete();

            $table->foreignId('branch_id')
                ->index()
                ->constrained('branches')
                ->restrictOnDelete();

            $table->foreignId('counter_id')
                ->constrained('counters')
                ->restrictOnDelete();

            $table->foreignId('quotation_id')
                ->nullable()
                ->index()
                ->constrained('quotations')
                ->nullOnDelete();

            $table->foreignId('customer_id')
                ->index()
                ->constrained('customers')
                ->restrictOnDelete();

            $table->foreignId('sales_person_id')
                ->index()
                ->constrained('users')
                ->restrictOnDelete();

            // Basic Invoice Information
            $table->unsignedBigInteger('invoice_no')->unique();
            $table->string('invoice_no_display', 100)->unique();
            $table->date('invoice_date')->index();

            // GST Type: 1 = CGST + SGST, 2 = IGST
            $table->tinyInteger('gst_type')->comment('1 = CGST + SGST, 2 = IGST');

            // Monetary Amount Fields
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('item_discount', 18, 2)->default(0);
            $table->decimal('invoice_discount', 18, 2)->default(0);
            $table->decimal('cgst_amount', 18, 2)->default(0);
            $table->decimal('sgst_amount', 18, 2)->default(0);
            $table->decimal('igst_amount', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('round_off', 18, 2)->default(0);
            $table->decimal('grand_total', 18, 2)->default(0);

            // Sale Type: 1 = Cash Sale, 2 = Credit Sale
            $table->tinyInteger('sale_type')->index()->comment('1 = Cash Sale, 2 = Credit Sale');
            $table->date('due_date')->nullable();

            // Status: 1 = Completed, 2 = Cancelled
            $table->tinyInteger('status')->default(1)->index()->comment('1 = Completed, 2 = Cancelled');

            // Remarks
            $table->text('remarks')->nullable();

            // Cancellation Details
            $table->foreignId('cancelled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->text('cancel_remarks')->nullable();

            // Audit Fields
            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
