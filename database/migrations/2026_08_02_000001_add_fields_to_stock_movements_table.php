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
        Schema::table('stock_movements', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_movements', 'counter_id')) {
                $table->foreignId('counter_id')
                    ->nullable()
                    ->after('branch_id')
                    ->constrained('counters')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('stock_movements', 'business_date')) {
                $table->date('business_date')
                    ->nullable()
                    ->after('movement_date')
                    ->index();
            }

            if (!Schema::hasColumn('stock_movements', 'unit_cost')) {
                $table->decimal('unit_cost', 18, 2)
                    ->nullable()
                    ->after('quantity');
            }

            if (!Schema::hasColumn('stock_movements', 'transaction_type')) {
                $table->unsignedTinyInteger('transaction_type')
                    ->nullable()
                    ->after('movement_type')
                    ->index();
            }

            if (!Schema::hasColumn('stock_movements', 'remarks')) {
                $table->text('remarks')
                    ->nullable()
                    ->after('reference_id');
            }

            $table->index(['company_id', 'product_id', 'branch_id', 'movement_date'], 'sm_comp_prod_br_date_idx');
            $table->index(['company_id', 'branch_id', 'movement_date'], 'sm_comp_br_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex('sm_comp_prod_br_date_idx');
            $table->dropIndex('sm_comp_br_date_idx');

            if (Schema::hasColumn('stock_movements', 'counter_id')) {
                $table->dropForeign(['counter_id']);
                $table->dropColumn('counter_id');
            }

            $columnsToDrop = [];
            foreach (['business_date', 'unit_cost', 'transaction_type', 'remarks'] as $col) {
                if (Schema::hasColumn('stock_movements', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
