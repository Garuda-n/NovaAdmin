<?php

namespace App\Enums;

enum StockTransferType: int
{
    case BRANCH = 1;
    case COUNTER = 2;

    public function label(): string
    {
        return match ($this) {
            self::BRANCH => 'Branch Transfer',
            self::COUNTER => 'Counter Transfer',
        };
    }
}
