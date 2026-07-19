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
        Schema::create('stock_inward_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_inward_id')->constrained('stock_inwards')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('sub_product_id')->nullable()->constrained('sub_products')->nullOnDelete();
            $table->decimal('qty', 12, 3);
            $table->decimal('weight', 12, 3)->nullable();
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->decimal('selling_price', 12, 2)->nullable();
            $table->decimal('mrp', 12, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_inward_items');
    }
};
