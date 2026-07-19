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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('hsn_code')->nullable();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->foreignId('uom_id')->constrained('uoms')->cascadeOnDelete();
            $table->foreignId('tax_id')->nullable()->constrained('taxes')->nullOnDelete();
            $table->string('tax_type')->default('exclusive'); // inclusive or exclusive
            $table->string('sales_based_on')->default('fixed'); // fixed or flexible
            $table->string('purchase_based_on')->default('fixed'); // fixed or flexible
            $table->boolean('has_size')->default(false);
            $table->string('calculation_based_on')->default('quantity'); // quantity, weight, sqft, dimension
            $table->boolean('reorder_applicable')->default(false);
            $table->decimal('min_stock_level', 10, 2)->nullable()->default(0);
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
