<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;

class DeviceMotionController extends Controller
{
    public function store(Request $request, Database $database)
    {
        $providedKey = (string) $request->header('X-DEVICE-KEY', '');
        $expectedKey = (string) config('intellifire.device_ingest_key', '');

        if ($expectedKey === '' || !hash_equals($expectedKey, $providedKey)) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'room_number' => ['required'],
            'motion' => ['required'],
            'timestamp' => ['nullable'],
        ]);

        $roomNumber = $data['room_number'];
        if (!is_numeric($roomNumber)) {
            return response()->json(['ok' => false, 'message' => 'room_number must be numeric'], 422);
        }
        $roomNumber = (int) $roomNumber;

        $motion = $data['motion'];
        if (is_string($motion)) {
            $m = strtolower(trim($motion));
            if (in_array($m, ['1', 'true', 'yes', 'y', 'on'], true)) {
                $motion = true;
            } elseif (in_array($m, ['0', 'false', 'no', 'n', 'off'], true)) {
                $motion = false;
            }
        }
        if (is_numeric($motion)) {
            $motion = ((int) $motion) === 1;
        }
        if (!is_bool($motion)) {
            return response()->json(['ok' => false, 'message' => 'motion must be boolean'], 422);
        }

        $roomsSnapshot = $database->getReference('rooms')->getSnapshot();
        $roomsRaw = $roomsSnapshot->getValue() ?? [];

        $roomId = null;
        if (is_array($roomsRaw) && $roomsRaw !== []) {
            foreach ($roomsRaw as $candidateId => $roomData) {
                if (!is_array($roomData)) continue;
                if (!isset($roomData['room_number']) || !is_numeric($roomData['room_number'])) continue;
                if (((int) $roomData['room_number']) === $roomNumber) {
                    $roomId = (string) $candidateId;
                    break;
                }
            }
        }

        if (!is_string($roomId) || $roomId === '') {
            return response()->json(['ok' => false, 'message' => 'Room not found for room_number'], 404);
        }

        $database->getReference('rooms/'.$roomId)->update([
            'motion' => $motion,
            'updated_at' => now()->toIso8601String(),
        ]);

        return response()->json(['ok' => true]);
    }
}
