<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceNotificationController;
use App\Http\Controllers\RoomsApiController;

Route::get('/', function () {
    return view('landing');
});

Route::get('/api/rooms', [RoomsApiController::class, 'index'])
    ->name('api.rooms');

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

Route::match(['get', 'post'], '/debug/firebase', function () {
    $b64 = env('FIREBASE_CREDENTIALS_JSON_BASE64');
    $creds = config('firebase.projects.app.credentials');
    return response()->json([
        'env_set' => $b64 !== null,
        'env_length' => strlen($b64 ?? 0),
        'config_type' => gettype($creds),
        'config_is_null' => $creds === null,
    ]);
})->name('debug.firebase');

Route::post('/device/notify', [DeviceNotificationController::class, 'store'])
    ->name('device.notify');
