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
        Schema::create('sales_details', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->foreignId('sales_id')
                ->index()
                ->constrained('sales')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->index()
                ->constrained('products')
                ->restrictOnDelete();

            $table->foreignId('uom_id')
                ->constrained('uoms')
                ->restrictOnDelete();

            $table->foreignId('allocated_item_id')
                ->nullable()
                ->index()
                ->constrained('stock_items')
                ->nullOnDelete();

            // Product Snapshot
            $table->string('product_code');
            $table->string('product_name');

            // Item Type: 1 = Allocated Item, 2 = Unallocated Item
            $table->tinyInteger('item_type')
                ->index()
                ->comment('1 = Allocated Item, 2 = Unallocated Item');

            // Quantity & Pricing
            $table->decimal('quantity', 18, 2)->default(0);
            $table->decimal('rate', 18, 2)->default(0);

            // Discount (discount_type: 1 = Percentage, 2 = Fixed Amount)
            $table->tinyInteger('discount_type')
                ->nullable()
                ->comment('1 = Percentage, 2 = Fixed Amount');
            $table->decimal('discount_value', 18, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);

            // GST Breakup
            $table->decimal('tax_percentage', 18, 2)->default(0);
            $table->decimal('cgst_percentage', 18, 2)->default(0);
            $table->decimal('cgst_amount', 18, 2)->default(0);
            $table->decimal('sgst_percentage', 18, 2)->default(0);
            $table->decimal('sgst_amount', 18, 2)->default(0);
            $table->decimal('igst_percentage', 18, 2)->default(0);
            $table->decimal('igst_amount', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);

            // Totals
            $table->decimal('line_total', 18, 2)->default(0);

            // Audit Timestamps
            $table->timestamps();

            // Composite Indexes for Reporting and Performance Optimization
            $table->index(['sales_id', 'product_id']);
            $table->index(['product_id', 'created_at']);
            $table->index(['sales_id', 'item_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_details');
    }
};
