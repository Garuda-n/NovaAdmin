<?php

namespace App\Enums;

enum StockItemStatus: int
{
    case AVAILABLE = 1;
    case COUNTER_TRANSFERRED = 2;
    case BRANCH_TRANSFERRED = 3;
    case RESERVED = 4;
    case SOLD = 5;
    case DAMAGED = 6;
    case UNDER_REPAIR = 7;
    case DISPOSED = 8;

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Available',
            self::COUNTER_TRANSFERRED => 'Counter Transferred',
            self::BRANCH_TRANSFERRED => 'Branch Transferred',
            self::RESERVED => 'Reserved',
            self::SOLD => 'Sold',
            self::DAMAGED => 'Damaged',
            self::UNDER_REPAIR => 'Under Repair',
            self::DISPOSED => 'Disposed',
        };
    }
}
