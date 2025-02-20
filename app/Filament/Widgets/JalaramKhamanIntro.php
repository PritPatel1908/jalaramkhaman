<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class JalaramKhamanIntro extends Widget
{
    protected static string $view = 'filament.widgets.jalaram-khaman-intro';

    protected static bool $isLazy = false;

    protected static ?int $sort = -1;

    public function getColumns(): int | string | array
    {

        return 2;
    }
}
