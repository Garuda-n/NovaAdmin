<?php

namespace App\Enums;

enum InventoryReferenceType: int
{
    case BULK_INWARD = 1;
    case STOCK_TRANSFER = 2;
    case SALES = 3;
    case SALES_RETURN = 4;
    case STOCK_ADJUSTMENT = 5;

    public function label(): string
    {
        return match ($this) {
            self::BULK_INWARD => 'Bulk Inward',
            self::STOCK_TRANSFER => 'Stock Transfer',
            self::SALES => 'Sales',
            self::SALES_RETURN => 'Sales Return',
            self::STOCK_ADJUSTMENT => 'Stock Adjustment',
        };
    }
}
