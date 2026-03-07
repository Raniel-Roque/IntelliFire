<?php

namespace App\Http\Controllers;

use Kreait\Firebase\Contract\Database;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationsApiController extends Controller
{
    public function latest(Request $request, Database $database)
    {
        $maxAgeMinutesRaw = $request->query('max_age_minutes', 60);
        $maxAgeMinutes = is_numeric($maxAgeMinutesRaw) ? (int) $maxAgeMinutesRaw : 60;
        if ($maxAgeMinutes <= 0) $maxAgeMinutes = 60;
        if ($maxAgeMinutes > 24 * 60) $maxAgeMinutes = 24 * 60;

        $cutoff = Carbon::now()->subMinutes($maxAgeMinutes);

        $snapshot = $database->getReference('emergencies/log')->getSnapshot();
        $raw = $snapshot->getValue() ?? [];

        $items = [];

        if (is_array($raw)) {
            foreach ($raw as $id => $data) {
                if (!is_string($id)) continue;
                if (!is_array($data)) continue;

                $rawTitle = $data['title'] ?? null;
                if (is_string($rawTitle)) {
                    $rawTitle = trim($rawTitle);
                    if ($rawTitle === '') $rawTitle = null;
                } else {
                    $rawTitle = null;
                }

                $level = strtolower((string) ($data['level'] ?? ''));
                $icon = $level === 'urgent' ? '🔴 ' : ($level === 'warning' ? '🟡 ' : '');

                $title = $rawTitle;
                $createdAtRaw = (string) ($data['created_at'] ?? '');
                if ($title !== null && $createdAtRaw !== '') {
                    try {
                        $dt = Carbon::parse($createdAtRaw)->timezone('Asia/Manila');
                        $title = $title.' | '.$dt->format('F j, Y').' | '.$dt->format('g:ia');
                    } catch (\Exception $e) {
                        // keep original title
                    }
                }

                if ($title !== null && $icon !== '') {
                    $title = $icon.$title;
                }

                $items[] = [
                    'created_at' => $createdAtRaw,
                    'title' => $title,
                ];
            }
        }

        $items = array_filter($items, function ($row) use ($cutoff) {
            $rawTs = (string) ($row['created_at'] ?? '');
            if ($rawTs === '') return false;
            try {
                $ts = Carbon::parse($rawTs);
            } catch (\Exception $e) {
                return false;
            }

            return $ts->gte($cutoff);
        });

        usort($items, function ($a, $b) {
            return strcmp((string) ($b['created_at'] ?? ''), (string) ($a['created_at'] ?? ''));
        });

        $items = array_values(array_slice($items, 0, 5));

        if (count($items) === 0) {
            $items = array_fill(0, 5, ['title' => ' ']);
            $items[0]['title'] = 'No problems detected';
        } else {
            for ($i = 0; $i < count($items); $i++) {
                $t = $items[$i]['title'] ?? null;
                $t = is_string($t) ? trim($t) : '';

                if ($i === 0) {
                    if ($t === '') {
                        $items[$i]['title'] = 'No problems detected';
                    }
                } else {
                    if ($t === '') {
                        $items[$i]['title'] = ' ';
                    }
                }
            }

            while (count($items) < 5) {
                $items[] = ['title' => ' '];
            }
        }

        $items = array_map(fn($row) => ['title' => $row['title'] ?? ' '], $items);

        return response()->json([
            'ok' => true,
            'count' => count($items),
            'notifications' => array_values($items),
        ], 200, [], JSON_PRETTY_PRINT);
    }
}
