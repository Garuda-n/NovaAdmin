<?php

namespace Database\Seeders;

use App\Models\PaymentMode;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentModeSeeder extends Seeder
{
    public function run(): void
    {
        $firstUser = User::first();
        $userId = $firstUser ? $firstUser->id : 1;

        $modes = [
            [
                'mode_name' => 'Cash',
                'mode_code' => 'CASH',
                'mode_type' => PaymentMode::TYPE_CASH,
                'display_order' => 1,
                'is_default' => true,
                'status' => PaymentMode::STATUS_ACTIVE,
                'created_by' => $userId,
            ],
            [
                'mode_name' => 'UPI',
                'mode_code' => 'UPI',
                'mode_type' => PaymentMode::TYPE_UPI,
                'display_order' => 2,
                'is_default' => false,
                'status' => PaymentMode::STATUS_ACTIVE,
                'created_by' => $userId,
            ],
            [
                'mode_name' => 'Credit / Debit Card',
                'mode_code' => 'CARD',
                'mode_type' => PaymentMode::TYPE_CARD,
                'display_order' => 3,
                'is_default' => false,
                'status' => PaymentMode::STATUS_ACTIVE,
                'created_by' => $userId,
            ],
            [
                'mode_name' => 'Bank Transfer (NEFT/RTGS/IMPS)',
                'mode_code' => 'BANK',
                'mode_type' => PaymentMode::TYPE_BANK,
                'display_order' => 4,
                'is_default' => false,
                'status' => PaymentMode::STATUS_ACTIVE,
                'created_by' => $userId,
            ],
            [
                'mode_name' => 'Cheque',
                'mode_code' => 'CHEQUE',
                'mode_type' => PaymentMode::TYPE_CHEQUE,
                'display_order' => 5,
                'is_default' => false,
                'status' => PaymentMode::STATUS_ACTIVE,
                'created_by' => $userId,
            ],
            [
                'mode_name' => 'Wallet',
                'mode_code' => 'WALLET',
                'mode_type' => PaymentMode::TYPE_WALLET,
                'display_order' => 6,
                'is_default' => false,
                'status' => PaymentMode::STATUS_ACTIVE,
                'created_by' => $userId,
            ],
        ];

        foreach ($modes as $mode) {
            PaymentMode::firstOrCreate(
                ['mode_code' => $mode['mode_code'], 'company_id' => null],
                $mode
            );
        }
    }
}
