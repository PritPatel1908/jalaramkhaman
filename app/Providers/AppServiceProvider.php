<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentAsset;
use Filament\Forms\Components\Field;
use App\Forms\Components\ProductSelector;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Field::macro('productSelector', function (string $name): ProductSelector {
            return ProductSelector::make($name);
        });
    }
}
