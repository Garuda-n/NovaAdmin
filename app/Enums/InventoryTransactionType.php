<?php

namespace App\Enums;

enum InventoryTransactionType: int
{
    case ALLOCATION = 1;
    case COUNTER_TRANSFER = 2;
    case BRANCH_TRANSFER = 3;
    case SALES = 4;
    case SALES_RETURN = 5;
    case DAMAGE = 6;
    case ADJUSTMENT = 7;
    case RESERVED = 8;
    case CANCELLED = 9;
    case REPAIR = 10;
    case DISPOSAL = 11;

    public function label(): string
    {
        return match ($this) {
            self::ALLOCATION => 'Allocation',
            self::COUNTER_TRANSFER => 'Counter Transfer',
            self::BRANCH_TRANSFER => 'Branch Transfer',
            self::SALES => 'Sales',
            self::SALES_RETURN => 'Sales Return',
            self::DAMAGE => 'Damage',
            self::ADJUSTMENT => 'Adjustment',
            self::RESERVED => 'Reserved',
            self::CANCELLED => 'Cancelled',
            self::REPAIR => 'Repair',
            self::DISPOSAL => 'Disposal',
        };
    }
}
