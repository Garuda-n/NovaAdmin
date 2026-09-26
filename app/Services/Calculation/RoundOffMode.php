<?php

namespace App\Services\Calculation;

enum RoundOffMode
{
    /** Grand total is the exact rounded sum; round-off is always 0 (Quotation behaviour). */
    case None;

    /** Grand total is auto-rounded to the nearest whole unit unless an override is supplied (Sales behaviour). */
    case NearestWhole;
}
