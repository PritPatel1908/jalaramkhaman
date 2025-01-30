<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Status: int implements HasLabel, HasIcon
{
    case Active = 1;
    case Inactive = 2;
    case Blocked = 3;
    case Deleted = 4;

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Active => 'heroicon-m-check',
            self::Inactive => 'heroicon-m-x-mark',
            self::Blocked => 'heroicon-m-x-mark',
            self::Deleted => 'heroicon-o-archive-box-x-mark',
        };
    }
}
