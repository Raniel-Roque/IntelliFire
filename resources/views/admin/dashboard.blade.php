<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Dashboard</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow p-6 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">Dashboard</h1>
                <p class="text-sm text-gray-600">Signed in as {{ auth()->user()->email }}</p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="px-4 py-2 rounded bg-black text-white" type="submit">Logout</button>
            </form>
        </div>

        <div class="mt-6 bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-700">Next: we will show device status and fire events here.</p>
        </div>
    </div>
</body>
</html>
