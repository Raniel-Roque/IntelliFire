<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;

class DeviceNotificationController extends Controller
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
            'flame' => ['nullable'],
            'gas' => ['nullable'],
            'type' => ['nullable', 'string'],
            'door_status' => ['nullable', 'string'],
            'door_command' => ['nullable', 'string'],
            'timestamp' => ['nullable'],
            'reason' => ['nullable', 'string'],
            'message' => ['nullable', 'string'],
        ]);

        $roomNumber = $data['room_number'];
        if (!is_numeric($roomNumber)) {
            return response()->json(['ok' => false, 'message' => 'room_number must be numeric'], 422);
        }
        $roomNumber = (int) $roomNumber;

        $type = strtolower((string) ($data['type'] ?? 'info'));
        $isEmergency = in_array($type, ['warning', 'urgent'], true);
        $isUpdate = $type === 'update';

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

        $flame = $data['flame'] ?? null;
        $gas = $data['gas'] ?? 0;

        if (is_string($flame)) {
            $f = strtolower(trim($flame));
            if (in_array($f, ['1', 'true', 'yes', 'y', 'on'], true)) {
                $flame = true;
            } elseif (in_array($f, ['0', 'false', 'no', 'n', 'off'], true)) {
                $flame = false;
            }
        }
        if (is_numeric($flame)) {
            $flame = ((int) $flame) === 1;
        }
        if (!is_bool($flame)) {
            $flame = false;
        }

        if (is_string($gas) && strtolower(trim($gas)) === 'n/a') {
            $gas = 0;
        }

        $gas = is_numeric($gas) ? (float) $gas : 0;

        $nowIso = now()->toIso8601String();

        $roomUpdate = [
            'flame' => $flame,
            'gas' => $gas,
            'updated_at' => $nowIso,
        ];

        if ($isEmergency) {
            $roomUpdate['emergency_level'] = $type;
            $roomUpdate['last_emergency_at'] = $nowIso;
        } else {
            $roomUpdate['emergency_level'] = null;
            $roomUpdate['last_info_at'] = $nowIso;
        }

        $database->getReference('rooms/'.$roomId)->update($roomUpdate);

        if ($isEmergency) {
            $reason = (string) ($data['reason'] ?? '');
            $message = (string) ($data['message'] ?? '');

            $doorStatus = isset($data['door_status']) ? strtolower(trim((string) $data['door_status'])) : null;
            if ($doorStatus !== null && !in_array($doorStatus, ['open', 'closed'], true)) {
                $doorStatus = null;
            }

            if ($doorStatus === null && isset($data['door_command'])) {
                $fallback = strtolower(trim((string) $data['door_command']));
                if (in_array($fallback, ['open', 'closed'], true)) {
                    $doorStatus = $fallback;
                }
            }

            $roomLabel = $roomName ?: ('Room '.$roomNumber);
            $levelLabel = strtoupper($type);

            $detail = '';
            if ($reason !== '') {
                $detail = $reason;
            } elseif ($message !== '') {
                $detail = $message;
            } elseif ($isUpdate) {
                $detail = $doorStatus !== null
                    ? ('Door status set to '.strtoupper($doorStatus))
                    : 'Update received';
            } else {
                $gasThreshold = (float) config('intellifire.emergency.gas_threshold', 1);
                $highGas = $gas >= $gasThreshold;

                $isWarning = $type === 'warning';

                if ($flame && $highGas) {
                    $detail = $isWarning
                        ? 'Flame and Elevated Gas Levels Detected'
                        : 'Flame and High Gas Levels Found';
                } elseif ($flame) {
                    $detail = $isWarning
                        ? 'Flame Detected'
                        : 'Fire Detected';
                } elseif ($highGas) {
                    $detail = $isWarning
                        ? 'Elevated Gas Level Detected'
                        : 'High Gas Levels Found';
                } else {
                    $detail = $isWarning
                        ? 'Readings Require Attention'
                        : 'Abnormal Readings Detected';
                }
            }

            $payload = [
                'room_number' => $roomNumber,
                'room_id' => $roomId,
                'room_name' => $roomName,
                'title' => $levelLabel.': '.$detail.' in '.$roomLabel,
                'reason' => $reason !== '' ? $reason : null,
                'message' => $message !== '' ? $message : null,
                'flame' => $flame,
                'gas' => $gas,
                'level' => $type,
                'door_status' => $doorStatus,
                'created_at' => $nowIso,
                'timestamp' => $data['timestamp'] ?? null,
            ];

            $database->getReference('emergencies/log')->push($payload);

            if ($isEmergency) {
                $database->getReference('emergencies/latest')->set($payload);
            }
        }

        return response()->json(['ok' => true]);
    }
}
