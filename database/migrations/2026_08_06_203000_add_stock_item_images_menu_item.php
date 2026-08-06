<?php

use App\Models\Menu;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $inventoryMenu = Menu::where('name', 'Inventory')->first();
        if ($inventoryMenu) {
            Menu::firstOrCreate(
                ['name' => 'Stock Item Images', 'parent_id' => $inventoryMenu->id],
                [
                    'route' => 'stock-item-images.index',
                    'icon' => 'camera',
                    'parent_id' => $inventoryMenu->id,
                    'permission_slug' => 'available-stock.view',
                    'order' => 4,
                    'status' => true,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Menu::where('name', 'Stock Item Images')->delete();
    }
};
