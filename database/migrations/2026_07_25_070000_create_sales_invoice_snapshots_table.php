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
        Schema::create('sales_invoice_snapshots', function (Blueprint $table) {
            $table->id();

            // Invoice Reference (One-to-One with sales table)
            $table->foreignId('sales_id')
                ->unique()
                ->constrained('sales')
                ->restrictOnDelete();

            // Customer Snapshot at billing time
            $table->string('customer_name');
            $table->string('customer_mobile', 20);
            $table->string('customer_email')->nullable();
            $table->text('customer_address')->nullable();
            $table->string('customer_gst_number', 50)->nullable()->index();

            // Company (Seller) Snapshot at billing time
            $table->string('company_name');
            $table->string('company_gst_number', 50)->nullable();
            $table->text('company_address')->nullable();

            // Branch Snapshot at billing time
            $table->string('branch_name');
            $table->string('branch_gst_number', 50)->nullable();
            $table->text('branch_address')->nullable();

            // Invoice Tax Snapshot
            $table->tinyInteger('gst_type')->comment('1 = CGST + SGST, 2 = IGST');

            // Additional Notes
            $table->text('notes')->nullable();

            // Audit Fields
            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Indexes
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoice_snapshots');
    }
};
