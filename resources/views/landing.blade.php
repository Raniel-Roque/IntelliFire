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

                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                                Log in
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
                <div class="max-w-2xl">
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
                        Fire monitoring, alerts, and device visibility.
                    </h1>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-300">
                        IntelliFire is your central dashboard for tracking fire events and device status.
                    </p>

                    <div class="mt-8 flex gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-5 py-3 text-sm font-medium rounded-lg bg-orange-600 hover:bg-orange-700 text-white">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center px-5 py-3 text-sm font-medium rounded-lg bg-orange-600 hover:bg-orange-700 text-white">
                                Sign in
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-layout>