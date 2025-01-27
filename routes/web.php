<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/main')->name('main');
Route::redirect('/admin/dashboard', '/admin')->name('admin');
