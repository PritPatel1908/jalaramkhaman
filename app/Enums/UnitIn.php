<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum UnitIn: int implements HasLabel
{
    case GRAM = 1;
    case KG = 2;
    case ML = 3;
    case LTR = 4;

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
