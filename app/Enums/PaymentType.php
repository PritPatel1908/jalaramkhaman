<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentType: int implements HasLabel
{
    case Online = 1;
    case Offline = 2;

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
