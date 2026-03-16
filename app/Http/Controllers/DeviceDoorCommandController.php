<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;

class DeviceDoorCommandController extends Controller
{
    public function show(Request $request, Database $database)
    {
        $roomsSnapshot = $database->getReference('rooms')->getSnapshot();
        $roomsRaw = $roomsSnapshot->getValue() ?? [];

        $roomNumberRaw = $request->query('room_number');
        if ($roomNumberRaw !== null && $roomNumberRaw !== '') {
            if (!is_numeric($roomNumberRaw)) {
                return response()->json(['ok' => false, 'message' => 'room_number must be numeric'], 422);
            }
            $roomNumber = (int) $roomNumberRaw;

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

            $status = strtolower(trim((string) ($room['door_status'] ?? 'closed')));
            if (!in_array($status, ['open', 'closed'], true)) {
                $status = 'closed';
            }

            return response()->json([
                'ok' => true,
                'room_id' => $roomId,
                'room_number' => $roomNumber,
                'door_status' => $status,
            ]);
        }

        $items = [];
        if (is_array($roomsRaw) && $roomsRaw !== []) {
            foreach ($roomsRaw as $id => $room) {
                if (!is_string($id)) continue;
                if (!is_array($room)) continue;

                $roomNumber = $room['room_number'] ?? null;
                if (!is_numeric($roomNumber)) continue;
                $roomNumber = (int) $roomNumber;

                $status = strtolower(trim((string) ($room['door_status'] ?? 'closed')));
                if (!in_array($status, ['open', 'closed'], true)) {
                    $status = 'closed';
                }

                $items[] = [
                    'room_id' => $id,
                    'room_number' => $roomNumber,
                    'door_status' => $status,
                ];
            }
        }

        usort($items, fn($a, $b) => ($a['room_number'] <=> $b['room_number']));

        return response()->json([
            'ok' => true,
            'count' => count($items),
            'rooms' => $items,
        ]);
    }
}
