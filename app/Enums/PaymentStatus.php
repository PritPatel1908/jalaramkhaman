<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: int implements HasLabel
{
    case Completed = 1;
    case Pending = 2;
    case Return = 3;

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
