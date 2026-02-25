<x-layout>
    <div class="min-h-screen flex flex-col">
        <header class="shadow-lg border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 sticky top-0 z-30">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <a href="{{ url('/') }}" class="flex items-center gap-3 text-gray-900 dark:text-gray-100 font-semibold">
                        <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <span>IntelliFire</span>
                    </a>

                    <div class="flex items-center gap-3">
                        <x-dark-mode-toggle />
                        <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Home
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
            <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h1 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Sign in</h1>

                <form id="firebase-login-form" class="space-y-4" method="POST" action="{{ route('auth.firebase.session') }}">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="email">Email</label>
                        <input class="mt-1 w-full border rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600" id="email" name="email" type="email" autocomplete="email" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="password">Password</label>
                        <input class="mt-1 w-full border rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600" id="password" name="password" type="password" autocomplete="current-password" required>
                    </div>

                    <div class="flex items-center justify-between">
                        <button class="px-4 py-2 rounded bg-orange-600 hover:bg-orange-700 text-white" type="submit">Login</button>
                    </div>

                    <p id="firebase-login-error" class="text-sm text-red-600 hidden"></p>
                </form>
            </div>
        </main>
    </div>
</x-layout>
