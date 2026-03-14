<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceNotificationController;
use App\Http\Controllers\RoomsApiController;
use App\Http\Controllers\NotificationsApiController;
use App\Http\Controllers\Auth\FirebaseSessionController;
use App\Http\Controllers\Auth\RoomUserSessionController;

Route::get('/', function () {
    return view('landing');
});

Route::get('/api/rooms', [RoomsApiController::class, 'index'])
    ->name('api.rooms');

Route::get('/api/notifications/latest', [NotificationsApiController::class, 'latest'])
    ->name('api.notifications.latest');

Route::view('/login', 'auth.login')->name('login');

Route::post('/auth/firebase/session', [FirebaseSessionController::class, 'store'])
    ->name('auth.firebase.session');

Route::post('/auth/room/session', [RoomUserSessionController::class, 'store'])
    ->name('auth.room.session');

Route::post('/logout', [FirebaseSessionController::class, 'destroy'])
    ->middleware('auth:web,room')
    ->name('logout');

Route::view('/dashboard', 'admin.dashboard')
    ->middleware('auth:web')
    ->name('dashboard');

Route::view('/change-password', 'auth.change-password')
    ->middleware('auth:web')
    ->name('change-password');

Route::view('/rooms', 'admin.rooms')
    ->middleware('auth:web')
    ->name('rooms');

Route::view('/user-home', 'user.home')
    ->middleware('auth:room')
    ->name('user.home');

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
