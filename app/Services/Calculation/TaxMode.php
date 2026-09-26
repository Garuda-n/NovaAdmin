<?php

namespace App\Services\Calculation;

enum TaxMode
{
    case Flat;
    case CgstSgst;
    case Igst;
}
