<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentVia: int implements HasLabel
{
    case Cash = 1;
    case UPI = 2;
    case NetBanking = 3;
    case CreditCard = 4;
    case DebitCard = 5;
    case Wallet = 6;
    case Other = 7;

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
