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
                'key'         => 'default_customer_type',
                'value'       => 'B2C',
                'group'       => 'customers',
                'type'        => 'string',
                'description' => 'Default customer registration type: B2C (Individual) or B2B (Corporate)',
            ],
            [
                'key'         => 'require_gst_b2b',
                'value'       => '1',
                'group'       => 'customers',
                'type'        => 'boolean',
                'description' => 'Mandatory 15-digit GSTIN validation for B2B customers (1 = Enabled, 0 = Optional)',
            ],
            [
                'key'         => 'supplier_scope',
                'value'       => 'Global',
                'group'       => 'suppliers',
                'type'        => 'string',
                'description' => 'Supplier Scope Policy: Global (accessible across all branches) or Branch (bound to specific branch)',
            ],
            [
                'key'         => 'default_credit_limit',
                'value'       => '0.00',
                'group'       => 'financial',
                'type'        => 'integer',
                'description' => 'Default credit limit amount (₹) allowed for new customer accounts',
            ],
            [
                'key'         => 'default_credit_days',
                'value'       => '30',
                'group'       => 'financial',
                'type'        => 'integer',
                'description' => 'Default credit payment period in days allowed for invoices',
            ],
            [
                'key'         => 'company_name',
                'value'       => 'NovaAdmin',
                'group'       => 'general',
                'type'        => 'string',
                'description' => 'System application title and business company name',
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
