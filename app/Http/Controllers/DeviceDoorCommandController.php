<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;

class DeviceDoorCommandController extends Controller
{
    public function show(Request $request, Database $database)
    {
        $roomNumberRaw = $request->query('room_number');
        if (!is_numeric($roomNumberRaw)) {
            return response()->json(['ok' => false, 'message' => 'room_number must be numeric'], 422);
        }
        $roomNumber = (int) $roomNumberRaw;

        $roomsSnapshot = $database->getReference('rooms')->getSnapshot();
        $roomsRaw = $roomsSnapshot->getValue() ?? [];

        $roomId = null;
        $room = null;

        if (is_array($roomsRaw) && $roomsRaw !== []) {
            foreach ($roomsRaw as $candidateId => $roomData) {
                if (!is_array($roomData)) continue;
                if (!isset($roomData['room_number']) || !is_numeric($roomData['room_number'])) continue;
                if (((int) $roomData['room_number']) === $roomNumber) {
                    $roomId = (string) $candidateId;
                    $room = $roomData;
                    break;
                }
            }
        }

        if (!is_string($roomId) || $roomId === '') {
            return response()->json(['ok' => false, 'message' => 'Room not found for room_number'], 404);
        }

        $command = strtolower(trim((string) ($room['door_command'] ?? 'closed')));
        if (!in_array($command, ['open', 'closed'], true)) {
            $command = 'closed';
        }

        return response()->json([
            'ok' => true,
            'room_id' => $roomId,
            'room_number' => $roomNumber,
            'door_command' => $command,
        ]);
    }
}
