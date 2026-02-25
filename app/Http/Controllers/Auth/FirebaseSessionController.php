<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class FirebaseSessionController extends Controller
{
    public function store(Request $request, FirebaseAuth $firebaseAuth)
    {
        $data = $request->validate([
            'id_token' => ['required', 'string'],
        ]);

        $verifiedToken = $firebaseAuth->verifyIdToken($data['id_token']);

        $uid = $verifiedToken->claims()->get('sub');
        $email = $verifiedToken->claims()->get('email');
        $name = $verifiedToken->claims()->get('name') ?? ($email ? explode('@', $email)[0] : $uid);

        $user = User::query()->where('firebase_uid', $uid)->first();

        if (!$user && $email) {
            $user = User::query()->where('email', $email)->first();
        }

        if (!$user) {
            $user = new User();
        }

        $user->firebase_uid = $uid;
        $user->email = $email ?? $user->email ?? ($uid.'@firebase.local');
        $user->name = $name ?? $user->name ?? 'User';

        if (!$user->password) {
            $user->password = Hash::make(Str::random(32));
        }
        $user->save();

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'ok' => true,
            'redirect' => route('dashboard'),
        ]);
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
