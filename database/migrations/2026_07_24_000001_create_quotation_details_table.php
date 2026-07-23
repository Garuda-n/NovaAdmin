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
        Schema::create('quotation_details', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('quotation_id')
                ->constrained('quotations')
                ->onDelete('restrict');
                
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('restrict');
                
            $table->string('product_name');
            
            $table->foreignId('uom_id')
                ->constrained('uoms')
                ->onDelete('restrict');
                
            $table->string('uom_name');
            
            $table->decimal('qty', 12, 3);
            $table->decimal('rate', 12, 2);
            $table->decimal('tax_percent', 5, 2)->default(0.00);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('line_total', 12, 2);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_details');
    }
};
