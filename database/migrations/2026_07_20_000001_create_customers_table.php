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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('mobile', 20)->index();
            $table->string('alternate_mobile', 20)->nullable();
            $table->string('email')->nullable();
            $table->enum('customer_type', ['B2C', 'B2B'])->default('B2C')->index();
            $table->string('gst_number', 20)->nullable();
            $table->text('address')->nullable();
            
            $table->foreignId('country_id')
                ->constrained('countries')
                ->onDelete('restrict');
                
            $table->foreignId('state_id')
                ->constrained('states')
                ->onDelete('restrict');
                
            $table->foreignId('city_id')
                ->constrained('cities')
                ->onDelete('restrict');
                
            $table->string('pincode', 10);
            
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->onDelete('set null');
                
            $table->decimal('credit_limit', 12, 2)->default(0.00);
            $table->integer('credit_days')->default(0);
            
            $table->string('created_through', 50)->default('Admin')->index();
            $table->boolean('status')->default(true)->index();
            
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
                
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
