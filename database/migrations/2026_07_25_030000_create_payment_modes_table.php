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
        Schema::create('payment_modes', function (Blueprint $table) {
            $table->id();

            // Scope: NULL = Global, non-NULL = Company-specific
            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->restrictOnDelete();

            // Payment Information
            $table->string('mode_name', 100);
            $table->string('mode_code', 30);

            // Mode Type: 1=Cash, 2=Bank, 3=UPI, 4=Card, 5=Cheque, 6=Wallet, 7=Other
            $table->tinyInteger('mode_type')
                ->index()
                ->comment('1=Cash, 2=Bank, 3=UPI, 4=Card, 5=Cheque, 6=Wallet, 7=Other');

            // Display & Default
            $table->integer('display_order')->default(0)->index();
            $table->boolean('is_default')->default(false);

            // Status: 1 = Active, 2 = Inactive
            $table->tinyInteger('status')
                ->default(1)
                ->index()
                ->comment('1 = Active, 2 = Inactive');

            // Optional JSON configuration for POS/Gateway/Terminal integrations
            $table->json('configuration')->nullable();

            // Remarks
            $table->text('remarks')->nullable();

            // Audit Fields
            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Indexes & Unique Constraints
            $table->unique(['company_id', 'mode_code']);
            $table->index(['status', 'display_order']);
            $table->index(['company_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_modes');
    }
};
