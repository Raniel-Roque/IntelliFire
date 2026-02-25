<?php

namespace App\Http\Controllers\Auth;

use App\Auth\FirebaseUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Kreait\Firebase\Contract\Database;

class FirebaseSessionController extends Controller
{
    public function store(Request $request, FirebaseAuth $firebaseAuth, Database $database)
    {
        $data = $request->validate([
            'id_token' => ['required', 'string'],
        ]);

        $verifiedToken = $firebaseAuth->verifyIdToken($data['id_token']);

        $uid = $verifiedToken->claims()->get('sub');
        $email = $verifiedToken->claims()->get('email');
        $name = $verifiedToken->claims()->get('name') ?? ($email ? explode('@', $email)[0] : $uid);

        $profile = [
            'firebase_uid' => $uid,
            'email' => $email,
            'name' => $name,
            'updated_at' => now()->toISOString(),
        ];

        $database->getReference('users/'.$uid)->update($profile);

        Auth::login(FirebaseUser::fromProfile($uid, $profile));
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
