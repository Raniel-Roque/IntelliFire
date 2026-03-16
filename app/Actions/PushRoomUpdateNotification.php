<?php

namespace App\Actions;

use Kreait\Firebase\Contract\Database;

class PushRoomUpdateNotification
{
    public static function push(Database $database, string $roomId, ?int $roomNumber, ?string $roomName, string $doorStatus): void
    {
        $roomLabel = $roomName ?: ($roomNumber !== null ? ('Room '.$roomNumber) : 'Room');
        $statusLabel = strtoupper($doorStatus);

        $payload = [
            'room_number' => $roomNumber,
            'room_id' => $roomId,
            'room_name' => $roomName,
            'title' => 'UPDATE: Door status set to '.$statusLabel.' in '.$roomLabel,
            'level' => 'update',
            'door_status' => $doorStatus,
            'created_at' => now()->toIso8601String(),
        ];

        $database->getReference('emergencies/log')->push($payload);
    }
}
