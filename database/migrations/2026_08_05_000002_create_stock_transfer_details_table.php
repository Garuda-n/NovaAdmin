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
        Schema::create('stock_transfer_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_transfer_id')->constrained('stock_transfers')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->tinyInteger('tracking_type')->comment('1: Quantity Tracking, 2: Individual Tracking');
            $table->foreignId('stock_item_id')->nullable()->constrained('stock_items');
            $table->string('item_code', 100)->nullable();
            $table->decimal('transferred_qty', 12, 2)->default(1.00);
            $table->decimal('received_qty', 12, 2)->nullable();
            $table->decimal('damaged_qty', 12, 2)->default(0.00);
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_details');
    }
};
