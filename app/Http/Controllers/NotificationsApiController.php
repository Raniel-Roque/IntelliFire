<?php

namespace App\Http\Controllers;

use Kreait\Firebase\Contract\Database;

class NotificationsApiController extends Controller
{
    public function latest(Database $database)
    {
        $snapshot = $database->getReference('emergencies/log')->getSnapshot();
        $raw = $snapshot->getValue() ?? [];

        $items = [];

        if (is_array($raw)) {
            foreach ($raw as $id => $data) {
                if (!is_string($id)) continue;
                if (!is_array($data)) continue;

                $level = strtolower((string) ($data['level'] ?? 'warning'));
                $status = $level === 'urgent' ? 'URGENT' : 'WARNING';

                $roomName = (string) ($data['room_name'] ?? '');
                if ($roomName === '') {
                    $roomNumber = $data['room_number'] ?? null;
                    $roomName = $roomNumber !== null ? ('Room '.$roomNumber) : '—';
                }

                $flame = $data['flame'] ?? false;
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

                if (is_string($gas) && strtolower(trim($gas)) === 'n/a') $gas = 0;

                $gas = is_numeric($gas) ? (float) $gas : 0;

                $description = (string) ($data['reason'] ?? '');
                if ($description === '') $description = (string) ($data['message'] ?? '');

                if ($description === '') {
                    $title = (string) ($data['title'] ?? '');
                    if ($title !== '') {
                        $title = preg_replace('/^(WARNING|URGENT)\s*:\s*/i', '', $title) ?? $title;
                        $title = preg_replace('/\s+in\s+.+$/i', '', $title) ?? $title;
                        $description = trim($title);
                    }
                }

                if ($description === '') $description = '—';

                $items[] = [
                    'id' => $id,
                    'created_at' => (string) ($data['created_at'] ?? ''),
                    'room_name' => $roomName,
                    'room_number' => $data['room_number'] ?? null,
                    'flame' => $flame,
                    'gas' => $gas,
                    'level' => $level,
                    'status' => $status,
                    'title' => $data['title'] ?? null,
                    'description' => $description,
                ];
            }
        }

        usort($items, function ($a, $b) {
            return strcmp((string) ($b['created_at'] ?? ''), (string) ($a['created_at'] ?? ''));
        });

        $items = array_slice($items, 0, 5);

        return response()->json([
            'ok' => true,
            'count' => count($items),
            'notifications' => array_values($items),
        ], 200, [], JSON_PRETTY_PRINT);
    }
}
