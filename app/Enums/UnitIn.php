<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum UnitIn: int implements HasLabel //, HasIcon
{
    case GRAM = 1;
    case KG = 2;
    case ML = 3;
    case LTR = 4;

    public function getLabel(): ?string
    {
        return $this->name;
    }

    // public function getIcon(): ?string
    // {
    //     return match ($this) {
    //         self::Start => 'heroicon-m-check',
    //         self::End => 'heroicon-m-x-mark',
    //         self::Deleted => 'heroicon-o-archive-box-x-mark',
    //     };
    // }
}
