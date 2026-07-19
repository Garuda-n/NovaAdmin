<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'group' => 'Dashboard'],

            // Administration
            ['name' => 'View Users', 'slug' => 'users.view', 'group' => 'Administration'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'group' => 'Administration'],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'group' => 'Administration'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'group' => 'Administration'],
            ['name' => 'View Roles', 'slug' => 'roles.view', 'group' => 'Administration'],
            ['name' => 'Create Roles', 'slug' => 'roles.create', 'group' => 'Administration'],
            ['name' => 'Edit Roles', 'slug' => 'roles.edit', 'group' => 'Administration'],
            ['name' => 'Delete Roles', 'slug' => 'roles.delete', 'group' => 'Administration'],

            // Masters
            ['name' => 'View Companies', 'slug' => 'companies.view', 'group' => 'Masters'],
            ['name' => 'Create Companies', 'slug' => 'companies.create', 'group' => 'Masters'],
            ['name' => 'Edit Companies', 'slug' => 'companies.edit', 'group' => 'Masters'],
            ['name' => 'Delete Companies', 'slug' => 'companies.delete', 'group' => 'Masters'],
            ['name' => 'View Branches', 'slug' => 'branches.view', 'group' => 'Masters'],
            ['name' => 'Create Branches', 'slug' => 'branches.create', 'group' => 'Masters'],
            ['name' => 'Edit Branches', 'slug' => 'branches.edit', 'group' => 'Masters'],
            ['name' => 'Delete Branches', 'slug' => 'branches.delete', 'group' => 'Masters'],

            // Catalog Masters
            ['name' => 'View Taxes', 'slug' => 'taxes.view', 'group' => 'Catalog Masters'],
            ['name' => 'Create Taxes', 'slug' => 'taxes.create', 'group' => 'Catalog Masters'],
            ['name' => 'Edit Taxes', 'slug' => 'taxes.edit', 'group' => 'Catalog Masters'],
            ['name' => 'Delete Taxes', 'slug' => 'taxes.delete', 'group' => 'Catalog Masters'],
            ['name' => 'View UOM', 'slug' => 'uoms.view', 'group' => 'Catalog Masters'],
            ['name' => 'Create UOM', 'slug' => 'uoms.create', 'group' => 'Catalog Masters'],
            ['name' => 'Edit UOM', 'slug' => 'uoms.edit', 'group' => 'Catalog Masters'],
            ['name' => 'Delete UOM', 'slug' => 'uoms.delete', 'group' => 'Catalog Masters'],
            ['name' => 'View Financial Years', 'slug' => 'financial-years.view', 'group' => 'Catalog Masters'],
            ['name' => 'Create Financial Years', 'slug' => 'financial-years.create', 'group' => 'Catalog Masters'],
            ['name' => 'Edit Financial Years', 'slug' => 'financial-years.edit', 'group' => 'Catalog Masters'],
            ['name' => 'Delete Financial Years', 'slug' => 'financial-years.delete', 'group' => 'Catalog Masters'],
            ['name' => 'View Counters', 'slug' => 'counters.view', 'group' => 'Catalog Masters'],
            ['name' => 'Create Counters', 'slug' => 'counters.create', 'group' => 'Catalog Masters'],
            ['name' => 'Edit Counters', 'slug' => 'counters.edit', 'group' => 'Catalog Masters'],
            ['name' => 'Delete Counters', 'slug' => 'counters.delete', 'group' => 'Catalog Masters'],

            // Product Masters
            ['name' => 'View Categories', 'slug' => 'categories.view', 'group' => 'Product Masters'],
            ['name' => 'Create Categories', 'slug' => 'categories.create', 'group' => 'Product Masters'],
            ['name' => 'Edit Categories', 'slug' => 'categories.edit', 'group' => 'Product Masters'],
            ['name' => 'Delete Categories', 'slug' => 'categories.delete', 'group' => 'Product Masters'],
            ['name' => 'View Brands', 'slug' => 'brands.view', 'group' => 'Product Masters'],
            ['name' => 'Create Brands', 'slug' => 'brands.create', 'group' => 'Product Masters'],
            ['name' => 'Edit Brands', 'slug' => 'brands.edit', 'group' => 'Product Masters'],
            ['name' => 'Delete Brands', 'slug' => 'brands.delete', 'group' => 'Product Masters'],
            ['name' => 'View Products', 'slug' => 'products.view', 'group' => 'Product Masters'],
            ['name' => 'Create Products', 'slug' => 'products.create', 'group' => 'Product Masters'],
            ['name' => 'Edit Products', 'slug' => 'products.edit', 'group' => 'Product Masters'],
            ['name' => 'Delete Products', 'slug' => 'products.delete', 'group' => 'Product Masters'],
            ['name' => 'View Sizes', 'slug' => 'sizes.view', 'group' => 'Product Masters'],
            ['name' => 'Create Sizes', 'slug' => 'sizes.create', 'group' => 'Product Masters'],
            ['name' => 'Edit Sizes', 'slug' => 'sizes.edit', 'group' => 'Product Masters'],
            ['name' => 'Delete Sizes', 'slug' => 'sizes.delete', 'group' => 'Product Masters'],

            // Menus
            ['name' => 'View Menus', 'slug' => 'menus.view', 'group' => 'Administration'],
            ['name' => 'Create Menus', 'slug' => 'menus.create', 'group' => 'Administration'],
            ['name' => 'Edit Menus', 'slug' => 'menus.edit', 'group' => 'Administration'],
            ['name' => 'Delete Menus', 'slug' => 'menus.delete', 'group' => 'Administration'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
