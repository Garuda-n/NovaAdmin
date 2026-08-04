<?php

namespace App\Enums;

enum StockTransferStatus: int
{
    case DRAFT = 1;
    case DISPATCHED = 2;
    case RECEIVED = 3;
    case CANCELLED = 4;

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::DISPATCHED => 'Dispatched',
            self::RECEIVED => 'Received',
            self::CANCELLED => 'Cancelled',
        };
    }

    /**
     * UI Status Label (derives "In Transit" from Dispatched)
     */
    public function uiLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::DISPATCHED => 'In Transit',
            self::RECEIVED => 'Received',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-secondary text-white',
            self::DISPATCHED => 'bg-info text-white',
            self::RECEIVED => 'bg-success text-white',
            self::CANCELLED => 'bg-danger text-white',
        };
    }
}
