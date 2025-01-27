<?php

namespace App\Filament\Pages\Auth;

use App\Helpers\AppLoginResponse;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Facades\Filament;
use Filament\Forms\Components;
use Filament\Notifications\Notification;
use Filament\Models\Contracts\FilamentUser;
use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Http\Responses\Auth\LoginResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class Login extends BaseLogin
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\TextInput::make('emailCode')
                    ->autofocus()
                    ->required()
                    ->autocomplete('emailCode')
                    ->placeholder(__('User Email Or Code')),
                Components\TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required()
                    ->autocomplete(false)
                    ->placeholder(__('Password')),
                Components\Checkbox::make('remember')
                    ->label(__('Remember Me')),
            ])
            ->columns(1);
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/login.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();

        // $last_url = (Session::get('last_active_url'));

        // $user = User::where('code', $data['emailCode'])->orWhere('email', $data['emailCode'])->first();
        $user = User::where('email', $data['emailCode'])->first();

        if (!$user) {
            Notification::make()
                ->title("Login Failed")
                ->body("Invalid credentials")
                ->danger()
                ->send();
            return null;
        }

        if (!password_verify($data['password'], $user->password)) {
            Notification::make()
                ->title("Login Failed")
                ->body("Invalid credentials")
                ->danger()
                ->send();
            return null;
        }

        if (strtolower($user->code) == 'superadmin') {
            Filament::auth()->login($user, true);
        } else {
            Filament::auth()->login($user, $data['remember'] ?? false);
        }

        $userGet = Filament::auth()->user();
        if (
            ($userGet instanceof FilamentUser) &&
            (!$userGet->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();
            $this->throwFailureValidationException();
        }

        session()->regenerate();
        session()->put('lastActivityTime', time());

        // if ($last_url) {
        //     session('last_active_url', $last_url);
        //     return app(AppLoginResponse::class);
        // }

        //return "Email has been sent.";
        return app(AppLoginResponse::class);
        // return app(LoginResponse::class);
    }
}
