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
        Schema::table('sales_payments', function (Blueprint $table) {
            $table->foreignId('sales_return_id')
                ->nullable()
                ->after('sales_id')
                ->index()
                ->constrained('sales_returns')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_payments', function (Blueprint $table) {
            $table->dropForeign(['sales_return_id']);
            $table->dropColumn('sales_return_id');
        });
    }
};
