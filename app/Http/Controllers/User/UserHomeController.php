<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Contract\Database;

class UserHomeController extends Controller
{
    public function show(Database $database)
    {
        $user = Auth::guard('room')->user();
        $roomId = $user?->getAuthIdentifier();

        $snapshot = $database->getReference('rooms/'.$roomId)->getSnapshot();
        $room = $snapshot->getValue();

        $doorStatus = 'closed';
        if (is_array($room) && isset($room['door_status'])) {
            $s = strtolower(trim((string) $room['door_status']));
            if (in_array($s, ['open', 'closed'], true)) {
                $doorStatus = $s;
            }
        }

        $response = 'no response';
        if (is_array($room) && isset($room['response'])) {
            $r = strtolower(trim((string) $room['response']));
            if (in_array($r, ['yes', 'no', 'no response'], true)) {
                $response = $r;
            }
        }

        $isUrgent = false;
        if (is_array($room) && isset($room['emergency_level'])) {
            $lvl = strtolower(trim((string) $room['emergency_level']));
            $isUrgent = $lvl === 'urgent';
        }

        return view('user.home', [
            'doorStatus' => $doorStatus,
            'response' => $response,
            'isUrgent' => $isUrgent,
        ]);
    }

    public function toggleDoor(Request $request, Database $database)
    {
        $user = Auth::guard('room')->user();
        $roomId = $user?->getAuthIdentifier();

        $snapshot = $database->getReference('rooms/'.$roomId)->getSnapshot();
        $room = $snapshot->getValue();

        $current = 'closed';
        if (is_array($room) && isset($room['door_status'])) {
            $s = strtolower(trim((string) $room['door_status']));
            if (in_array($s, ['open', 'closed'], true)) {
                $current = $s;
            }
        }

        $next = $current === 'open' ? 'closed' : 'open';

        $database->getReference('rooms/'.$roomId)->update([
            'door_status' => $next,
            'updated_at' => now()->toIso8601String(),
        ]);

        return redirect()->route('user.home');
    }

    public function setResponse(Request $request, Database $database)
    {
        $data = $request->validate([
            'response' => ['required', 'string'],
        ]);

        $response = strtolower(trim((string) $data['response']));
        if (!in_array($response, ['yes', 'no'], true)) {
            return redirect()->route('user.home');
        }

        $user = Auth::guard('room')->user();
        $roomId = $user?->getAuthIdentifier();

        $database->getReference('rooms/'.$roomId)->update([
            'response' => $response,
            'updated_at' => now()->toIso8601String(),
        ]);

        return redirect()->route('user.home');
    }
}
