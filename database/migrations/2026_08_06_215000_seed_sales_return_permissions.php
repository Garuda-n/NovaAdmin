<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The permissions to seed.
     */
    protected array $permissions = [
        [
            'name' => 'View Sales Returns',
            'slug' => 'sales-returns.view',
            'group' => 'Sales',
        ],
        [
            'name' => 'Create Sales Returns',
            'slug' => 'sales-returns.create',
            'group' => 'Sales',
        ],
        [
            'name' => 'View Returned Stock',
            'slug' => 'returned-stock.view',
            'group' => 'Inventory',
        ],
        [
            'name' => 'Recreate Returned Stock',
            'slug' => 'returned-stock.recreate',
            'group' => 'Inventory',
        ],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();
        foreach ($this->permissions as $perm) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $perm['slug']],
                [
                    'name' => $perm['name'],
                    'group' => $perm['group'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $slugs = array_column($this->permissions, 'slug');
        DB::table('permissions')->whereIn('slug', $slugs)->delete();
    }
};
