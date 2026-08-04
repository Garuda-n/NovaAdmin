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
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->string('transfer_no', 50)->unique();
            $table->tinyInteger('transfer_type')->comment('1: Branch Transfer, 2: Counter Transfer');
            $table->foreignId('source_branch_id')->constrained('branches');
            $table->foreignId('source_counter_id')->nullable()->constrained('counters');
            $table->foreignId('destination_branch_id')->constrained('branches');
            $table->foreignId('destination_counter_id')->nullable()->constrained('counters');
            $table->date('transfer_date');
            $table->tinyInteger('status')->default(1)->comment('1: Draft, 2: Dispatched, 3: Received, 4: Cancelled');
            $table->text('remarks')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            // Audit and approval fields
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->dateTime('approved_at')->nullable();
            $table->foreignId('dispatched_by')->nullable()->constrained('users');
            $table->dateTime('dispatched_at')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users');
            $table->dateTime('received_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users');
            $table->dateTime('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};
