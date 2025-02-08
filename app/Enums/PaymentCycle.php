<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentCycle: int implements HasLabel, HasIcon
{
    case Daily = 1;
    case Weekly = 2;
    case Monthly = 3;

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Daily => 'heroicon-m-check',
            self::Weekly => 'heroicon-m-x-mark',
            self::Monthly => 'heroicon-m-x-mark',
        };
    }
}
