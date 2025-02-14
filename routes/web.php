<?php

use App\Filament\Pages\Auth\Login;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/main')->name('main');
Route::redirect('/admin/dashboard', '/admin')->name('admin');

// Route::get('/login', Login::class)->name('login');
