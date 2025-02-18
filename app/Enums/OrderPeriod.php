<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OrderPeriod: int implements HasLabel
{
    case Daily = 1;
    case Weekly = 2;
    case Monthly = 3;

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
