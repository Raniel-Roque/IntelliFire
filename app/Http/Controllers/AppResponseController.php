<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;

class AppResponseController extends Controller
{
    public function store(Request $request, Database $database)
    {
        $data = $request->validate([
            'room_number' => ['nullable'],
            'room_id' => ['nullable'],
            'response' => ['required'],
            'timestamp' => ['nullable'],
        ]);

        $roomNumber = $data['room_number'] ?? $data['room_id'] ?? null;
        if ($roomNumber === null || !is_numeric($roomNumber)) {
            return response()->json(['ok' => false, 'message' => 'room_number must be numeric'], 422);
        }
        $roomNumber = (int) $roomNumber;

        $resp = $data['response'];
        if (is_string($resp)) {
            $v = strtolower(trim($resp));
            if (in_array($v, ['1', 'true', 'yes', 'y', 'on'], true)) {
                $resp = true;
            } elseif (in_array($v, ['0', 'false', 'no', 'n', 'off'], true)) {
                $resp = false;
            }
        }
        if (is_numeric($resp)) {
            $resp = ((int) $resp) === 1;
        }
        if (!is_bool($resp)) {
            return response()->json(['ok' => false, 'message' => 'response must be boolean'], 422);
        }

        $value = $resp ? 'yes' : 'no';

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
            'response' => $value,
            'updated_at' => now()->toIso8601String(),
        ]);

        return response()->json(['ok' => true]);
    }
}
