<?php

namespace App\Http\Controllers\Auth;

use App\Auth\RoomUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Contract\Database;

class RoomUserSessionController extends Controller
{
    public function store(Request $request, Database $database)
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $username = trim($data['username']);
        $password = (string) $data['password'];

        $snapshot = $database->getReference('room_users')->getSnapshot();
        $raw = $snapshot->getValue() ?? [];

        $matchedRoomId = null;
        $matchedProfile = null;

        if (is_array($raw)) {
            foreach ($raw as $roomId => $profile) {
                if (!is_array($profile)) continue;

                $u = isset($profile['username']) ? (string) $profile['username'] : '';
                if ($u === $username) {
                    $matchedRoomId = (string) $roomId;
                    $matchedProfile = $profile;
                    break;
                }
            }
        }

        if (!$matchedRoomId || !$matchedProfile) {
            return back()->withErrors(['username' => 'Invalid username or password.'])->onlyInput('username');
        }

        $storedPassword = isset($matchedProfile['password']) ? (string) $matchedProfile['password'] : '';
        if (!hash_equals($storedPassword, $password)) {
            return back()->withErrors(['username' => 'Invalid username or password.'])->onlyInput('username');
        }

        Auth::guard('room')->login(RoomUser::fromProfile($matchedRoomId, $matchedProfile));
        $request->session()->regenerate();

        return redirect()->route('user.home');
    }
}
