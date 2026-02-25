<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::view('/login', 'auth.login')->name('login');

Route::post('/auth/firebase/session', [\App\Http\Controllers\Auth\FirebaseSessionController::class, 'store'])
    ->name('auth.firebase.session');

Route::post('/logout', [\App\Http\Controllers\Auth\FirebaseSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::view('/dashboard', 'admin.dashboard')
    ->middleware('auth')
    ->name('dashboard');

Route::view('/change-password', 'auth.change-password')
    ->middleware('auth')
    ->name('change-password');

Route::view('/rooms', 'admin.rooms')
    ->middleware('auth')
    ->name('rooms');
