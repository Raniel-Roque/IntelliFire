<x-layout>
    <div class="min-h-screen flex flex-col">
        <header class="shadow-lg border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 sticky top-0 z-30">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <a href="{{ url('/') }}" class="flex items-center gap-3 text-gray-900 dark:text-gray-100 font-semibold">
                        <img src="{{ asset('Logo.jpg') }}" alt="IntelliFire" class="w-8 h-8 rounded-lg object-cover" />
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

                <div class="mb-4" x-data="{ mode: 'user' }">
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" @click="mode = 'user'" class="px-3 py-2 rounded border text-sm font-medium" :class="mode === 'user' ? 'bg-orange-600 text-white border-orange-600' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600'">User</button>
                        <button type="button" @click="mode = 'admin'" class="px-3 py-2 rounded border text-sm font-medium" :class="mode === 'admin' ? 'bg-orange-600 text-white border-orange-600' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600'">Admin</button>
                    </div>

                    <form x-show="mode === 'user'" class="space-y-4 mt-4" method="POST" action="{{ route('auth.room.session') }}">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="username">Username</label>
                            <input class="mt-1 w-full border rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600" id="username" name="username" type="text" autocomplete="username" required>
                            @error('username')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-data="{ show: false }">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="room_password">Password</label>
                            <div class="relative mt-1">
                                <input class="w-full border rounded px-3 py-2 pr-10 bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600" id="room_password" name="password" :type="show ? 'text' : 'password'" autocomplete="current-password" required>
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white" aria-label="Toggle password visibility">
                                    <svg x-show="!show" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 3c-4.418 0-8 4.5-8 7s3.582 7 8 7 8-4.5 8-7-3.582-7-8-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Z" />
                                        <path d="M10 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z" />
                                    </svg>
                                    <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20C7 20 2.73 16.11 1 12c.74-1.78 1.86-3.41 3.29-4.77" />
                                        <path d="M10.58 10.58a2 2 0 0 0 2.83 2.83" />
                                        <path d="M9.88 4.24A10.94 10.94 0 0 1 12 4c5 0 9.27 3.89 11 8-1 2.43-2.8 4.53-5.06 5.94" />
                                        <path d="M1 1l22 22" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <button class="px-4 py-2 rounded bg-orange-600 hover:bg-orange-700 text-white" type="submit">Login</button>
                        </div>
                    </form>

                    <form x-show="mode === 'admin'" id="firebase-login-form" class="space-y-4 mt-4" method="POST" action="{{ route('auth.firebase.session') }}">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="email">Email</label>
                            <input class="mt-1 w-full border rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600" id="email" name="email" type="email" autocomplete="email" required>
                        </div>

                        <div x-data="{ show: false }">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="password">Password</label>
                            <div class="relative mt-1">
                                <input class="w-full border rounded px-3 py-2 pr-10 bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600" id="password" name="password" :type="show ? 'text' : 'password'" autocomplete="current-password" required>
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white" aria-label="Toggle password visibility">
                                    <svg x-show="!show" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 3c-4.418 0-8 4.5-8 7s3.582 7 8 7 8-4.5 8-7-3.582-7-8-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Z" />
                                        <path d="M10 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z" />
                                    </svg>
                                    <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20C7 20 2.73 16.11 1 12c.74-1.78 1.86-3.41 3.29-4.77" />
                                        <path d="M10.58 10.58a2 2 0 0 0 2.83 2.83" />
                                        <path d="M9.88 4.24A10.94 10.94 0 0 1 12 4c5 0 9.27 3.89 11 8-1 2.43-2.8 4.53-5.06 5.94" />
                                        <path d="M1 1l22 22" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <button class="px-4 py-2 rounded bg-orange-600 hover:bg-orange-700 text-white" type="submit">Login</button>
                        </div>

                        <p id="firebase-login-error" class="text-sm text-red-600 hidden"></p>
                    </form>
                </div>
            </div>
        </main>
    </div>
</x-layout>
