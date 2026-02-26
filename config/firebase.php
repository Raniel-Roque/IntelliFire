<?php

declare(strict_types=1);

$credentialsJsonBase64 = env('FIREBASE_CREDENTIALS_JSON_BASE64') 
    ?: 'ew0KICAidHlwZSI6ICJzZXJ2aWNlX2FjY291bnQiLA0KICAicHJvamVjdF9pZCI6ICJpbnRlbGxpZmlyZS1hZWZmMCIsDQogICJwcml2YXRlX2tleV9pZCI6ICJhMjEyOGEzM2Y4ZDI4NDFkMjA5ODUxNzliMWUyOWEzMzQwNWFhOTIyIiwNCiAgInByaXZhdGVfa2V5IjogIi0tLS0tQkVHSU4gUFJJVkFURSBLRVktLS0tLVxuTUlJRXZBSUJBREFOQmdrcWhraUc5dzBCQVFFRkFBU0NCS1l3Z2dTaUFnRUFBb0lCQVFESXNvQ3J4SmJkdzJTclxuRnhSVTl6QjUxais4dmVha1FNbzhDcXpTVzI4R3ByckF0M0R4Um5Hc3ZYd0kyR1Z0cXM0STBPTHg2RmlQWGcvSFxuQ3g1NFV5eE5GaWh2RVhjSGljRXJQcjgwRGxyNzFNMFhoYXEwY0puUEF1c1dSRXhoa3dMZEh0WGZBYml0RDZFOFxuS0RxQ2R1d2dybDljVmRtVk5vaFRXVlE0K25jSVVoWTB3VTJIZng1cTBXL2MrdFRmZUFadk5sMSt6dDY1Szk2YlxuSUFlZnR3NWcrRHF6RHVmbG1DZGtjQ3h6TmxrNDhFaEJJdkRKdjdUbzQrblV2V1AyQWJXVzdITThXUSt1MkRmM1xuNDdHZW55eXNSZk5TYXQxdHJ3R1JTTFBUdHFkRktGaHdsdlJBVjV4dGtZSDI0S2VxaE5BNGxaVVMvRmhQNUpqZFxuR1RZMnNZc1ZBZ01CQUFFQ2dnRUFCdkluN045R3YyY1VucTArVHJzb0NpekZrNXZZNnlTemtHOW9KWGIwK1R4MFxuL2RnbUhiRHI4ZWdKbVJRYTNUaEtTQzRwT2VrVTJPVmtVMlcwU01MTUo0dUFSbWtnS2FVZ0I5dEo2bkhOYVZEUlxuOFBkTWZmdmdWRFNoZSttaU5uQk1RRDNoeTFyaVpZUkRJWE5jK2JORzlIeVp5cUY0QUh6YzFxYjMrTDdidHNYSFxuZDJoTFNTTzA4UWQ0YTcxUk8xK0wvZWJtWmFwRFMyNUlYanFZUGowSVJlZ0RFZEJGZHBLeXhvb2x5OCt6TFAvYlxuRjhhYldvS0xVSmFKdUpERC9SUGJCaFloYkVPNU5naS9ad2k5ZDNOYkc1cG5qa00vbmF1czA0eC9walZRSVgwZVxuTFJzTmpYZGJQQjJacmd2ZkxBeUgxSzRwTkl3c0tNYjJOM2hWR1k3dU13S0JnUUR3eFowc0Yrb2ZjMXF2WU9yUFxuKzZXOE9ueWppL09kNTJyQVkwZWp0VS83QWhwNlc1bE1WNmxKWDRuZDdVOXExazQ4NWNESmRadlkycGZSRzAyQVxuK1JNM1ZKbnpWVlFSQXJwZTR2NDY5SjZmRmZuR1RzNUU1U2x3eG1MNEg0dkJTOVNmZ3EyMDlwM3Z4bUtiV0RoOVxuMzR5MjB5T3BTZDg3MzVQWHJSQ1pnclAzc3dLQmdRRFZaQWd4YzV2Q1FXYS8wa1FFSW9HUlpKTUVXNktLMW9FOVxuVDhJajA0anZMMXJaQmN5MWxuUi8zSlBORWVBeXdvYy9PUnNidnlZbno5eHZ6dGg0WFo2TnNOSG44SVY2TFBWV1xuQjlnYmlsejNIelFGOWxXWkEwQUFwOVdnbkgwYUpBQUlOaTBlV1AzSkZHdEFSbzhMYkFHNXFXUTlUdC9SN3dnelxuZEM0K3hkdU9Gd0tCZ0dWbXhKLzZYbWJxZlNuWmhtc2JqNXhyZ1d5YmwzbE1La0JtVDVpWjF0cGU4Mm5PeEF4TFxuNndiMHpxcUJ0RzNKWGQvMTN6QzVjRzV1K1h0ZXBWQ0pGZGU2c0srem94a2E3Z1RpVXJIZnJpSlA3Z2JmejYxaVxuV2dhZDlyYUxDcC9ZNE81ZzZlbUo1OUJBMit5U3hnLzFJMTBvSlIrNTllTlFjUUpuWjlOUElaNVRBb0dBTFdselxuWU9yRWZBdWxEUzkzTUJZVDB0Zy9mVTd2QVhMeTRCUm9NVzJrRjVyUlQ1d3ZXM21nWTFHUzFySjJMdmM5QnhSbFxuRTN1VGFDZVJOdTRqSFVKM0twbzFvWkdMSmx3SS9mei9YYVVOY2IzZk9XR2FCODBzUXJkMC9CQXhnMjJqNEJ0L1xuY0pUcHRYQTEvdURGTWw0UzQrc0xuUXg3RDFjNlBKRCtBbmZpNkpNQ2dZQXB0clhSSjk4OFlhdWtsZDVndWJlRVxuQldPZzFGR0VPNEhKbDNIdWJWcU92RTFNTVRTWUlWSXkyMElnM1I2K0MyQm1aNmhYdlVYbVNQb1F2Mk93cWdNa1xuNXM2UWt6RS9WaXoyVjBGMHhwbGFnSWIxVXEwV2F1M1RwdEJYMWUxQUxVL2xHNnFLZjNabjU4KzR4MzlBMFlrQlxuQ1hoKzh2dFJrWWJVeGZjNHlFTzdLQT09XG4tLS0tLUVORCBQUklWQVRFIEtFWS0tLS0tXG4iLA0KICAiY2xpZW50X2VtYWlsIjogImZpcmViYXNlLWFkbWluc2RrLWZic3ZjQGludGVsbGlmaXJlLWFlZmYwLmlhbS5nc2VydmljZWFjY291bnQuY29tIiwNCiAgImNsaWVudF9pZCI6ICIxMDcxNTg0MDk5ODkwODIxODY2MDUiLA0KICAiYXV0aF91cmkiOiAiaHR0cHM6Ly9hY2NvdW50cy5nb29nbGUuY29tL28vb2F1dGgyL2F1dGgiLA0KICAidG9rZW5fdXJpIjogImh0dHBzOi8vb2F1dGgyLmdvb2dsZWFwaXMuY29tL3Rva2VuIiwNCiAgImF1dGhfcHJvdmlkZXJfeDUwOV9jZXJ0X3VybCI6ICJodHRwczovL3d3dy5nb29nbGVhcGlzLmNvbS9vYXV0aDIvdjEvY2VydHMiLA0KICAiY2xpZW50X3g1MDlfY2VydF91cmwiOiAiaHR0cHM6Ly93d3cuZ29vZ2xlYXBpcy5jb20vcm9ib3QvdjEvbWV0YWRhdGEveDUwOS9maXJlYmFzZS1hZG1pbnNkay1mYnN2YyU0MGludGVsbGlmaXJlLWFlZmYwLmlhbS5nc2VydmljZWFjY291bnQuY29tIiwNCiAgInVuaXZlcnNlX2RvbWFpbiI6ICJnb29nbGVhcGlzLmNvbSINCn0NCg==';
$credentialsJson = env('FIREBASE_CREDENTIALS_JSON');

if (is_string($credentialsJsonBase64) && $credentialsJsonBase64 !== '') {
    $decoded = base64_decode($credentialsJsonBase64, true);
    if (is_string($decoded) && $decoded !== '') {
        $credentialsJson = $decoded;
    }
}

$credentials = env('FIREBASE_CREDENTIALS')
    ?: ($credentialsJson ? json_decode($credentialsJson, true) : env('GOOGLE_APPLICATION_CREDENTIALS'));

if (is_string($credentials) && $credentials !== '') {
    $isWindowsAbsolutePath = (bool) preg_match('/^[A-Za-z]:\\\\/', $credentials);
    $isUnixAbsolutePath = str_starts_with($credentials, '/');

    if (!$isWindowsAbsolutePath && !$isUnixAbsolutePath) {
        $candidate = base_path($credentials);
        if (is_file($candidate)) {
            $credentials = $candidate;
        }
    }
}

return [
    /*
     * ------------------------------------------------------------------------
     * Default Firebase project
     * ------------------------------------------------------------------------
     */

    'default' => env('FIREBASE_PROJECT', 'app'),

    /*
     * ------------------------------------------------------------------------
     * Firebase project configurations
     * ------------------------------------------------------------------------
     */

    'projects' => [
        'app' => [

            /*
             * ------------------------------------------------------------------------
             * Credentials / Service Account
             * ------------------------------------------------------------------------
             *
             * In order to access a Firebase project and its related services using a
             * server SDK, requests must be authenticated. For server-to-server
             * communication this is done with a Service Account.
             *
             * If you don't already have generated a Service Account, you can do so by
             * following the instructions from the official documentation pages at
             *
             * https://firebase.google.com/docs/admin/setup#initialize_the_sdk
             *
             * Once you have downloaded the Service Account JSON file, you can use it
             * to configure the package.
             *
             * If you don't provide credentials, the Firebase Admin SDK will try to
             * auto-discover them
             *
             * - by checking the environment variable FIREBASE_CREDENTIALS
             * - by checking the environment variable GOOGLE_APPLICATION_CREDENTIALS
             * - by trying to find Google's well known file
             * - by checking if the application is running on GCE/GCP
             *
             * If no credentials file can be found, an exception will be thrown the
             * first time you try to access a component of the Firebase Admin SDK.
             *
             */

            'credentials' => $credentials,

            /*
             * ------------------------------------------------------------------------
             * Firebase Auth Component
             * ------------------------------------------------------------------------
             */

            'auth' => [
                'tenant_id' => env('FIREBASE_AUTH_TENANT_ID'),
            ],

            /*
             * ------------------------------------------------------------------------
             * Firestore Component
             * ------------------------------------------------------------------------
             */

            'firestore' => [

                /*
                 * If you want to access a Firestore database other than the default database,
                 * enter its name here.
                 *
                 * By default, the Firestore client will connect to the `(default)` database.
                 *
                 * https://firebase.google.com/docs/firestore/manage-databases
                 */

                // 'database' => env('FIREBASE_FIRESTORE_DATABASE'),
            ],

            /*
             * ------------------------------------------------------------------------
             * Firebase Realtime Database
             * ------------------------------------------------------------------------
             */

            'database' => [

                /*
                 * In most of the cases the project ID defined in the credentials file
                 * determines the URL of your project's Realtime Database. If the
                 * connection to the Realtime Database fails, you can override
                 * its URL with the value you see at
                 *
                 * https://console.firebase.google.com/u/1/project/_/database
                 *
                 * Please make sure that you use a full URL like, for example,
                 * https://my-project-id.firebaseio.com
                 */

                'url' => env('FIREBASE_DATABASE_URL'),

                /*
                 * As a best practice, a service should have access to only the resources it needs.
                 * To get more fine-grained control over the resources a Firebase app instance can access,
                 * use a unique identifier in your Security Rules to represent your service.
                 *
                 * https://firebase.google.com/docs/database/admin/start#authenticate-with-limited-privileges
                 */

                // 'auth_variable_override' => [
                //     'uid' => 'my-service-worker'
                // ],

            ],

            /*
             * ------------------------------------------------------------------------
             * Firebase Cloud Storage
             * ------------------------------------------------------------------------
             */

            'storage' => [

                /*
                 * Your project's default storage bucket usually uses the project ID
                 * as its name. If you have multiple storage buckets and want to
                 * use another one as the default for your application, you can
                 * override it here.
                 */

                'default_bucket' => env('FIREBASE_STORAGE_DEFAULT_BUCKET'),

            ],

            /*
             * ------------------------------------------------------------------------
             * Caching
             * ------------------------------------------------------------------------
             *
             * The Firebase Admin SDK can cache some data returned from the Firebase
             * API, for example Google's public keys used to verify ID tokens.
             *
             */

            'cache_store' => env('FIREBASE_CACHE_STORE', 'file'),

            /*
             * ------------------------------------------------------------------------
             * Logging
             * ------------------------------------------------------------------------
             *
             * Enable logging of HTTP interaction for insights and/or debugging.
             *
             * Log channels are defined in config/logging.php
             *
             * Successful HTTP messages are logged with the log level 'info'.
             * Failed HTTP messages are logged with the log level 'notice'.
             *
             * Note: Using the same channel for simple and debug logs will result in
             * two entries per request and response.
             */

            'logging' => [
                'http_log_channel' => env('FIREBASE_HTTP_LOG_CHANNEL'),
                'http_debug_log_channel' => env('FIREBASE_HTTP_DEBUG_LOG_CHANNEL'),
            ],

            /*
             * ------------------------------------------------------------------------
             * HTTP Client Options
             * ------------------------------------------------------------------------
             *
             * Behavior of the HTTP Client performing the API requests
             */

            'http_client_options' => [

                /*
                 * Use a proxy that all API requests should be passed through.
                 * (default: none)
                 */

                'proxy' => env('FIREBASE_HTTP_CLIENT_PROXY'),

                /*
                 * Set the maximum amount of seconds (float) that can pass before
                 * a request is considered timed out
                 *
                 * The default time out can be reviewed at
                 * https://github.com/beste/firebase-php/blob/6.x/src/Firebase/Http/HttpClientOptions.php
                 */

                'timeout' => env('FIREBASE_HTTP_CLIENT_TIMEOUT'),

                'guzzle_middlewares' => [
                    // MyInvokableMiddleware::class,
                    // [MyMiddleware::class, 'static_method'],
                ],
            ],
        ],
    ],
];
