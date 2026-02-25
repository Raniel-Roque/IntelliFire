<?php

return [
    'device_ingest_key' => env('DEVICE_INGEST_KEY', ''),

    'emergency' => [
        'temp_threshold' => env('EMERGENCY_TEMP_THRESHOLD', 60),
        'gas_threshold' => env('EMERGENCY_GAS_THRESHOLD', 1),
    ],
];
