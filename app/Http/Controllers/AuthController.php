<?php

namespace App\Http\Controllers;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        // $credentials = $request->validate([
        //     'email' => ['required', 'email'],
        //     'password' => ['required'],
        // ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        if (!password_verify($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'The provided credentials do not match our records.',
            ])->onlyInput('password');
        }

        if (strtolower($user->code) == 'superadmin') {
            Filament::auth()->login($user, true);
        } else {
            Filament::auth()->login($user, $request->remember ?? false);
        }

        $userGet = Filament::auth()->user();
        if (
            ($userGet instanceof FilamentUser) &&
            (!$userGet->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();
            $this->throwFailureValidationException();
        }

        // if (Auth::attempt($credentials)) {
        //     $request->session()->regenerate();
        //     return redirect()->intended('dashboard');
        // }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
