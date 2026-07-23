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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_no')->nullable()->unique();
            $table->date('business_date');
            
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->onDelete('restrict');
                
            $table->foreignId('counter_id')
                ->constrained('counters')
                ->onDelete('restrict');
                
            $table->foreignId('customer_id')
                ->constrained('customers')
                ->onDelete('restrict');
                
            $table->enum('customer_type', ['B2B', 'B2C']);
            $table->tinyInteger('status')->default(1)->comment('1=Created, 2=Converted');
            
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2)->default(0.00);
            
            $table->text('remarks')->nullable();
            
            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('restrict');
                
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('restrict');
                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
