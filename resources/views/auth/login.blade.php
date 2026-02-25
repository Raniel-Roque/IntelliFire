<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Login</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50 p-6">
    <div class="w-full max-w-md bg-white rounded-lg shadow p-6">
        <h1 class="text-xl font-semibold mb-4">Sign in</h1>

        <form id="firebase-login-form" class="space-y-4" method="POST" action="{{ route('auth.firebase.session') }}">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700" for="email">Email</label>
                <input class="mt-1 w-full border rounded px-3 py-2" id="email" name="email" type="email" autocomplete="email" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="password">Password</label>
                <input class="mt-1 w-full border rounded px-3 py-2" id="password" name="password" type="password" autocomplete="current-password" required>
            </div>

            <div class="flex items-center justify-between">
                <button class="px-4 py-2 rounded bg-black text-white" type="submit">Login</button>
                <a class="text-sm text-gray-600 underline" href="/">Home</a>
            </div>

            <p id="firebase-login-error" class="text-sm text-red-600 hidden"></p>
        </form>
    </div>
</body>
</html>
