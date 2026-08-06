<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Seed "Sales Returns" under the "Sales" dropdown menu parent
        $salesParentId = DB::table('menus')->where('name', 'Sales')->value('id');
        if ($salesParentId) {
            DB::table('menus')->updateOrInsert(
                [
                    'name' => 'Sales Returns',
                    'parent_id' => $salesParentId,
                ],
                [
                    'route' => 'sales-returns.index',
                    'icon' => 'arrow-uturn-left',
                    'permission_slug' => 'sales-returns.view',
                    'order' => 4,
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 2. Seed "Returned Stock" under the "Inventory" dropdown menu parent
        $inventoryParentId = DB::table('menus')->where('name', 'Inventory')->value('id');
        if ($inventoryParentId) {
            DB::table('menus')->updateOrInsert(
                [
                    'name' => 'Returned Stock',
                    'parent_id' => $inventoryParentId,
                ],
                [
                    'route' => 'returned-stock.index',
                    'icon' => 'arrow-path',
                    'permission_slug' => 'returned-stock.view',
                    'order' => 4,
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('menus')->whereIn('route', ['sales-returns.index', 'returned-stock.index'])->delete();
    }
};
