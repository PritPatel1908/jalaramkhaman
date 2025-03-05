<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Status: int implements HasLabel
{
    case Start = 1;
    case End = 2;
    case Deleted = 3;
    case Waiting = 4;
    case Waiting_For_Dispatch = 5;
    case Delivered = 6;
    case Pause = 7;

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
