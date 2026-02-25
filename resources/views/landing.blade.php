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
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-10 items-start">
                    <section class="lg:col-span-3 order-1" aria-label="Latest notifications">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Latest Notifications</h2>
                            <span class="text-xs text-gray-500 dark:text-gray-400" id="landing-notifs-status">Live</span>
                        </div>

                        <div class="mt-4 grid grid-cols-1 gap-3" data-firebase-notifications-feed>
                            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                                <p class="text-sm text-gray-600 dark:text-gray-300">No notifications yet.</p>
                            </div>
                        </div>

                        <div id="fire-exit-maps-mobile" class="mt-8 lg:hidden scroll-mt-24">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Fire Exit Maps</h2>
                            <div class="mt-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4" x-data="{ selected: '{{ asset('maps/Room 101.png') }}' }" @select-fire-exit-map.window="selected = $event.detail.url">
                                <p class="text-sm text-gray-600 dark:text-gray-300">Choose a map to view the fire exit routes.</p>

                                <div class="mt-4">
                                    <select x-model="selected" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                                        <option value="{{ asset('maps/Room 101.png') }}">Room 101</option>
                                        <option value="{{ asset('maps/Room 102.png') }}">Room 102</option>
                                        <option value="{{ asset('maps/Room 201.png') }}">Room 201</option>
                                        <option value="{{ asset('maps/Room 202.png') }}">Room 202</option>
                                    </select>
                                </div>

                                <div class="mt-4 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                    <img :src="selected" alt="Selected fire exit map" class="w-full h-72 object-contain bg-white dark:bg-gray-900" loading="lazy" />
                                </div>

                                <a :href="selected" target="_blank" rel="noopener" class="mt-4 inline-flex w-full items-center justify-center px-4 py-2 rounded-lg bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium">View Map</a>
                            </div>
                        </div>
                    </section>

                    <aside class="lg:col-span-2 order-2" aria-label="Emergency contacts">
                        <div id="fire-exit-maps-desktop" class="hidden lg:block scroll-mt-24">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Fire Exit Maps</h2>
                            <div class="mt-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4" x-data="{ selected: '{{ asset('maps/Room 101.png') }}' }" @select-fire-exit-map.window="selected = $event.detail.url">
                                <p class="text-sm text-gray-600 dark:text-gray-300">Choose a map to view the fire exit routes.</p>

                                <div class="mt-4">
                                    <select x-model="selected" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                                        <option value="{{ asset('maps/Room 101.png') }}">Room 101</option>
                                        <option value="{{ asset('maps/Room 102.png') }}">Room 102</option>
                                        <option value="{{ asset('maps/Room 201.png') }}">Room 201</option>
                                        <option value="{{ asset('maps/Room 202.png') }}">Room 202</option>
                                    </select>
                                </div>

                                <div class="mt-4 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                    <img :src="selected" alt="Selected fire exit map" class="w-full h-80 object-contain bg-white dark:bg-gray-900" loading="lazy" />
                                </div>

                                <a :href="selected" target="_blank" rel="noopener" class="mt-4 inline-flex w-full items-center justify-center px-4 py-2 rounded-lg bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium">View Map</a>
                            </div>
                        </div>

                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mt-4">Emergency Contacts</h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Use these numbers in case of a fire emergency.</p>

                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-3">
                            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Fire Department</p>
                                <a href="tel:+6391234567890" class="mt-1 inline-block text-lg font-semibold text-gray-900 dark:text-gray-100 hover:underline">
                                    +63 9123 456 7890
                                </a>
                            </div>
                            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Owner</p>
                                <a href="tel:+6391234567890" class="mt-1 inline-block text-lg font-semibold text-gray-900 dark:text-gray-100 hover:underline">
                                    +63 9123 456 7890
                                </a>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </main>
    </div>
</x-layout>