<?php

namespace App\Helpers;

use Filament\Facades\Filament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Filament\Http\Responses\Auth\LoginResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as Responsable;

class AppLoginResponse extends LoginResponse
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        if (Filament::auth()->user()->user_type == 'admin') {
            return redirect(route('admin'));
        } else {
            return redirect(route('main'));
        }
        // return redirect(session()->get('last_active_url'));
    }
}
