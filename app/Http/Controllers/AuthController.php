<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Filament\Models\Contracts\FilamentUser;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

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

        if ($userGet->user_type === 'admin') {
            return redirect()->route(Filament::getPanel('admin')->getUrl());
        } else {
            return redirect()->route('main');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
