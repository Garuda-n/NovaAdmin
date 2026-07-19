<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            [
                'key'         => 'customer_scope',
                'value'       => 'Global',
                'group'       => 'customers',
                'type'        => 'string',
                'description' => 'Customer Scope Policy: Global (accessible across all branches) or Branch (bound to specific branch)',
            ],
            [
                'key'         => 'supplier_scope',
                'value'       => 'Global',
                'group'       => 'suppliers',
                'type'        => 'string',
                'description' => 'Supplier Scope Policy: Global (accessible across all branches) or Branch (bound to specific branch)',
            ],
            [
                'key'         => 'branch_wise_customer',
                'value'       => '0',
                'group'       => 'customers',
                'type'        => 'boolean',
                'description' => 'Branch-wise Customer Policy: 1 = Enforce branch association for customers, 0 = Global customer pool',
            ],
        ];

        foreach ($defaultSettings as $st) {
            Setting::updateOrCreate(
                ['key' => $st['key']],
                $st
            );
        }
    }
}
