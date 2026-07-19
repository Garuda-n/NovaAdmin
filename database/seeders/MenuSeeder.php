<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Dashboard
        Menu::firstOrCreate(
            ['name' => 'Dashboard'],
            [
                'route' => 'dashboard',
                'icon' => 'home',
                'parent_id' => null,
                'permission_slug' => 'dashboard.view',
                'order' => 1,
                'status' => true,
            ]
        );

        // Catalog Masters (Dropdown parent)
        $catalog = Menu::firstOrCreate(
            ['name' => 'Catalog Masters'],
            [
                'route' => null,
                'icon' => 'squares-2x2',
                'parent_id' => null,
                'permission_slug' => null,
                'order' => 2,
                'status' => true,
            ]
        );

        $catalogChildren = [
            ['name' => 'Taxes', 'route' => 'taxes.index', 'icon' => 'users', 'permission_slug' => 'taxes.view', 'order' => 1],
            ['name' => 'UOM', 'route' => 'uoms.index', 'icon' => 'scale', 'permission_slug' => 'uoms.view', 'order' => 2],
            ['name' => 'Financial Year', 'route' => 'financial-years.index', 'icon' => 'calendar-days', 'permission_slug' => 'financial-years.view', 'order' => 3],
            ['name' => 'Counters', 'route' => 'counters.index', 'icon' => 'ticket', 'permission_slug' => 'counters.view', 'order' => 4],
        ];

        foreach ($catalogChildren as $child) {
            Menu::firstOrCreate(
                ['name' => $child['name'], 'parent_id' => $catalog->id],
                array_merge($child, ['parent_id' => $catalog->id, 'status' => true])
            );
        }

        // Administration (Dropdown parent)
        $admin = Menu::firstOrCreate(
            ['name' => 'Administration'],
            [
                'route' => null,
                'icon' => 'building-office-2',
                'parent_id' => null,
                'permission_slug' => null,
                'order' => 3,
                'status' => true,
            ]
        );

        $adminChildren = [
            ['name' => 'Users', 'route' => 'users.index', 'icon' => 'users', 'permission_slug' => 'users.view', 'order' => 1],
            ['name' => 'Roles', 'route' => 'roles.index', 'icon' => 'shield-check', 'permission_slug' => 'roles.view', 'order' => 2],
            ['name' => 'Menus', 'route' => 'menus.index', 'icon' => 'bars-3', 'permission_slug' => 'menus.view', 'order' => 3],
            ['name' => 'Settings', 'route' => 'settings.index', 'icon' => 'cog-6-tooth', 'permission_slug' => 'settings.view', 'order' => 4],
        ];

        foreach ($adminChildren as $child) {
            Menu::firstOrCreate(
                ['name' => $child['name'], 'parent_id' => $admin->id],
                array_merge($child, ['parent_id' => $admin->id, 'status' => true])
            );
        }

        // Masters (Dropdown parent)
        $masters = Menu::firstOrCreate(
            ['name' => 'Masters'],
            [
                'route' => null,
                'icon' => 'building-library',
                'parent_id' => null,
                'permission_slug' => null,
                'order' => 4,
                'status' => true,
            ]
        );

        $mastersChildren = [
            ['name' => 'Companies', 'route' => 'companies.index', 'icon' => 'building-office', 'permission_slug' => 'companies.view', 'order' => 1],
            ['name' => 'Branches', 'route' => 'branches.index', 'icon' => 'map-pin', 'permission_slug' => 'branches.view', 'order' => 2],
            ['name' => 'Customers', 'route' => 'customers.index', 'icon' => 'user-group', 'permission_slug' => 'customers.view', 'order' => 3],
            ['name' => 'Suppliers', 'route' => 'suppliers.index', 'icon' => 'truck', 'permission_slug' => 'suppliers.view', 'order' => 4],
        ];

        foreach ($mastersChildren as $child) {
            Menu::firstOrCreate(
                ['name' => $child['name'], 'parent_id' => $masters->id],
                array_merge($child, ['parent_id' => $masters->id, 'status' => true])
            );
        }

        // Product Masters (Dropdown parent)
        $products = Menu::firstOrCreate(
            ['name' => 'Product Masters'],
            [
                'route' => null,
                'icon' => 'cube',
                'parent_id' => null,
                'permission_slug' => null,
                'order' => 5,
                'status' => true,
            ]
        );

        $productChildren = [
            ['name' => 'Categories', 'route' => 'categories.index', 'icon' => 'rectangle-group', 'permission_slug' => 'categories.view', 'order' => 1],
            ['name' => 'Brands', 'route' => 'brands.index', 'icon' => 'tag', 'permission_slug' => 'brands.view', 'order' => 2],
            ['name' => 'Products', 'route' => 'products.index', 'icon' => 'cube', 'permission_slug' => 'products.view', 'order' => 3],
            ['name' => 'Sizes', 'route' => 'sizes.index', 'icon' => 'arrows-right-left', 'permission_slug' => 'sizes.view', 'order' => 4],
            ['name' => 'Sub Products', 'route' => 'sub-products.index', 'icon' => 'squares-plus', 'permission_slug' => 'sub-products.view', 'order' => 5],
        ];

        foreach ($productChildren as $child) {
            Menu::firstOrCreate(
                ['name' => $child['name'], 'parent_id' => $products->id],
                array_merge($child, ['parent_id' => $products->id, 'status' => true])
            );
        }

        // Inventory (Dropdown parent)
        $inventory = Menu::firstOrCreate(
            ['name' => 'Inventory'],
            [
                'route' => null,
                'icon' => 'archive-box',
                'parent_id' => null,
                'permission_slug' => null,
                'order' => 6,
                'status' => true,
            ]
        );

        $inventoryChildren = [
            ['name' => 'Bulk Inward', 'route' => 'stock-inwards.index', 'icon' => 'arrow-down-tray', 'permission_slug' => 'stock-inwards.view', 'order' => 1],
        ];

        foreach ($inventoryChildren as $child) {
            Menu::firstOrCreate(
                ['name' => $child['name'], 'parent_id' => $inventory->id],
                array_merge($child, ['parent_id' => $inventory->id, 'status' => true])
            );
        }
    }
}
