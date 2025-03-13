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
    case NO = 5;

    public function getLabel(): string
    {
        return match ($this) {
            self::GRAM => 'Gram',
            self::KG => 'Kilogram',
            self::ML => 'Milliliter',
            self::LTR => 'Liter',
            self::NO => 'No',
        };
    }

    public static function asSelectArray(): array
    {
        return [
            self::GRAM->value => self::GRAM->getLabel(),
            self::KG->value => self::KG->getLabel(),
            self::ML->value => self::ML->getLabel(),
            self::LTR->value => self::LTR->getLabel(),
            self::NO->value => self::NO->getLabel(),
        ];
    }
}
