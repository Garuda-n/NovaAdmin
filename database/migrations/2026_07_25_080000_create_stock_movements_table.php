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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete();

            $table->foreignId('branch_id')
                ->constrained('branches')
                ->restrictOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            $table->foreignId('stock_item_id')
                ->nullable()
                ->constrained('stock_items')
                ->nullOnDelete();

            // Movement Types: 1=Opening, 2=Purchase, 3=Sale, 4=Transfer, 5=Adjustment, 6=Return
            $table->tinyInteger('movement_type')
                ->index()
                ->comment('1=Opening, 2=Purchase, 3=Sale, 4=Transfer, 5=Adjustment, 6=Return');

            $table->decimal('quantity', 18, 2);

            $table->string('reference_type', 100)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->date('movement_date')->index();

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();

            // Indexes for stock valuation & audit reports
            $table->index(['product_id', 'branch_id']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
