<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;

class DeviceNotificationController extends Controller
{
    public function store(Request $request, Database $database)
    {
        $providedKey = (string) $request->header('X-DEVICE-KEY', '');
        $expectedKey = (string) env('DEVICE_INGEST_KEY', '');

        if ($expectedKey === '' || !hash_equals($expectedKey, $providedKey)) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'room_number' => ['required'],
            'temp' => ['nullable'],
            'gas' => ['nullable'],
            'type' => ['nullable', 'string'],
            'timestamp' => ['nullable'],
        ]);

        $roomNumber = $data['room_number'];
        if (!is_numeric($roomNumber)) {
            return response()->json(['ok' => false, 'message' => 'room_number must be numeric'], 422);
        }
        $roomNumber = (int) $roomNumber;

        $type = strtolower((string) ($data['type'] ?? 'info'));
        $isEmergency = in_array($type, ['warning', 'urgent'], true);

        $roomId = null;
        $roomName = null;

        $roomsSnapshot = $database->getReference('rooms')->getSnapshot();
        $roomsRaw = $roomsSnapshot->getValue() ?? [];

        if (is_array($roomsRaw) && $roomsRaw !== []) {
            foreach ($roomsRaw as $candidateId => $roomData) {
                if (!is_array($roomData)) continue;
                if (!isset($roomData['room_number']) || !is_numeric($roomData['room_number'])) continue;

                if (((int) $roomData['room_number']) === $roomNumber) {
                    $roomId = (string) $candidateId;
                    $roomName = isset($roomData['name']) ? (string) $roomData['name'] : null;
                    break;
                }
            }
        }

        if (!is_string($roomId) || $roomId === '') {
            return response()->json(['ok' => false, 'message' => 'Room not found for room_number'], 404);
        }

        $temperature = $data['temp'] ?? 0;
        $gas = $data['gas'] ?? 0;

        if (is_string($temperature) && strtolower(trim($temperature)) === 'n/a') {
            $temperature = 0;
        }
        if (is_string($gas) && strtolower(trim($gas)) === 'n/a') {
            $gas = 0;
        }

        $temperature = is_numeric($temperature) ? (float) $temperature : 0;
        $gas = is_numeric($gas) ? (float) $gas : 0;

        $nowIso = now()->toISOString();

        $roomUpdate = [
            'temperature' => $temperature,
            'gas' => $gas,
            'updated_at' => $nowIso,
        ];

        if ($isEmergency) {
            $roomUpdate['emergency_level'] = $type;
            $roomUpdate['last_emergency_at'] = $nowIso;
        } else {
            $roomUpdate['last_info_at'] = $nowIso;
        }

        $database->getReference('rooms/'.$roomId)->update($roomUpdate);

        if ($isEmergency) {
            $payload = [
                'room_number' => $roomNumber,
                'room_id' => $roomId,
                'room_name' => $roomName,
                'temperature' => $temperature,
                'gas' => $gas,
                'level' => $type,
                'created_at' => $nowIso,
                'timestamp' => $data['timestamp'] ?? null,
            ];

            $database->getReference('emergencies/latest')->set($payload);
            $database->getReference('emergencies/log')->push($payload);
        }

        return response()->json(['ok' => true]);
    }
}
