<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;

class AppDoorStatusController extends Controller
{
    public function store(Request $request, Database $database)
    {
        $data = $request->validate([
            'room_number' => ['nullable'],
            'room_id' => ['nullable'],
            'door_status' => ['required'],
            'timestamp' => ['nullable'],
        ]);

        $roomNumber = $data['room_number'] ?? $data['room_id'] ?? null;
        if ($roomNumber === null || !is_numeric($roomNumber)) {
            return response()->json(['ok' => false, 'message' => 'room_number must be numeric'], 422);
        }
        $roomNumber = (int) $roomNumber;

        $door = $data['door_status'];
        if (is_string($door)) {
            $v = strtolower(trim($door));
            if (in_array($v, ['1', 'true', 'yes', 'y', 'on', 'open'], true)) {
                $door = true;
            } elseif (in_array($v, ['0', 'false', 'no', 'n', 'off', 'closed', 'close'], true)) {
                $door = false;
            }
        }
        if (is_numeric($door)) {
            $door = ((int) $door) === 1;
        }
        if (!is_bool($door)) {
            return response()->json(['ok' => false, 'message' => 'door_status must be boolean'], 422);
        }

        $status = $door ? 'open' : 'closed';

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
            'door_command' => $status,
            'updated_at' => now()->toIso8601String(),
        ]);

        return response()->json(['ok' => true]);
    }
}
