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
        Schema::create('sales_return_details', function (Blueprint $table) {
            $table->id();

            // Foreign Key Relations
            $table->foreignId('sales_return_id')
                ->index()
                ->constrained('sales_returns')
                ->cascadeOnDelete();

            $table->foreignId('sales_detail_id')
                ->index()
                ->constrained('sales_details')
                ->restrictOnDelete();

            $table->foreignId('product_id')
                ->index()
                ->constrained('products')
                ->restrictOnDelete();

            $table->foreignId('uom_id')
                ->constrained('uoms')
                ->restrictOnDelete();

            // Dual Stock Item Mapping
            $table->foreignId('original_stock_item_id')
                ->nullable()
                ->index()
                ->constrained('stock_items')
                ->restrictOnDelete();

            $table->foreignId('recreated_stock_item_id')
                ->nullable()
                ->index()
                ->constrained('stock_items')
                ->restrictOnDelete();

            // Line Item Details
            $table->tinyInteger('item_type')
                ->index()
                ->comment('1 = Allocated/Serialized, 2 = Unallocated/Quantity');

            $table->decimal('returned_quantity', 18, 2)->default(0);
            $table->decimal('rate', 18, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);

            // GST Calculations
            $table->decimal('tax_percentage', 18, 2)->default(0);
            $table->decimal('cgst_percentage', 18, 2)->default(0);
            $table->decimal('cgst_amount', 18, 2)->default(0);
            $table->decimal('sgst_percentage', 18, 2)->default(0);
            $table->decimal('sgst_amount', 18, 2)->default(0);
            $table->decimal('igst_percentage', 18, 2)->default(0);
            $table->decimal('igst_amount', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);

            $table->decimal('line_total', 18, 2)->default(0);

            $table->timestamps();

            // Indexes for Reporting
            $table->index(['sales_return_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_return_details');
    }
};
