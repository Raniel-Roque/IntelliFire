<?php

namespace App\Http\Controllers;

use Kreait\Firebase\Contract\Database;

class RoomsApiController extends Controller
{
    public function index(Database $database)
    {
        $snapshot = $database->getReference('rooms')->getSnapshot();
        $raw = $snapshot->getValue() ?? [];

        $items = [];

        if (is_array($raw)) {
            foreach ($raw as $id => $room) {
                if (!is_array($room)) continue;

                $roomNumber = $room['room_number'] ?? null;
                if (is_numeric($roomNumber)) {
                    $roomNumber = (int) $roomNumber;
                } else {
                    $roomNumber = null;
                }

                $name = isset($room['name']) ? (string) $room['name'] : null;

                $temperature = $room['temperature'] ?? 0;
                $gas = $room['gas'] ?? 0;

                if (is_string($temperature) && strtolower(trim($temperature)) === 'n/a') $temperature = 0;
                if (is_string($gas) && strtolower(trim($gas)) === 'n/a') $gas = 0;

                $temperature = is_numeric($temperature) ? (float) $temperature : 0;
                $gas = is_numeric($gas) ? (float) $gas : 0;

                $level = strtolower((string) ($room['emergency_level'] ?? ''));
                $status = $level === 'urgent' ? 'URGENT' : ($level === 'warning' ? 'WARNING' : 'NORMAL');

                $items[] = [
                    'id' => (string) $id,
                    'room_number' => $roomNumber,
                    'room_name' => $name ?: ($roomNumber !== null ? ('Room '.$roomNumber) : null),
                    'temp' => $temperature,
                    'gas' => $gas,
                    'status' => $status,
                ];
            }
        }

        usort($items, function ($a, $b) {
            $an = $a['room_number'] ?? null;
            $bn = $b['room_number'] ?? null;
            if (is_int($an) && is_int($bn)) return $an <=> $bn;
            if (is_int($an)) return -1;
            if (is_int($bn)) return 1;
            return strcmp((string) ($a['room_name'] ?? ''), (string) ($b['room_name'] ?? ''));
        });

        return response()->json([
            'ok' => true,
            'count' => count($items),
            'rooms' => $items,
        ]);
    }
}
