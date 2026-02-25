<x-layout>
    <x-navbar title="Change Password" :includeSidebar="true" :user="auth()->user()">
        <div class="container mx-auto px-4 pb-8 pt-4">
            <div class="max-w-md mx-auto">
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Change Password</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update your account password</p>
                    </div>

                    <div class="p-6">
                        <form id="firebase-change-password-form" class="space-y-4">
                            <div x-data="{ show: false }">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="current_password">Current Password</label>
                                <div class="relative mt-1">
                                    <input class="w-full border rounded px-3 py-2 pr-10 bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600" id="current_password" name="current_password" :type="show ? 'text' : 'password'" autocomplete="current-password" required>
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

                            <div x-data="{ show: false }">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="new_password">New Password</label>
                                <div class="relative mt-1">
                                    <input class="w-full border rounded px-3 py-2 pr-10 bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600" id="new_password" name="new_password" :type="show ? 'text' : 'password'" autocomplete="new-password" minlength="8" required>
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
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Minimum 8 characters</p>
                            </div>

                            <div x-data="{ show: false }">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="new_password_confirmation">Confirm New Password</label>
                                <div class="relative mt-1">
                                    <input class="w-full border rounded px-3 py-2 pr-10 bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600" id="new_password_confirmation" name="new_password_confirmation" :type="show ? 'text' : 'password'" autocomplete="new-password" minlength="8" required>
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

                            <p id="firebase-change-password-error" class="text-sm text-red-600 hidden"></p>
                            <p id="firebase-change-password-success" class="text-sm text-green-600 hidden"></p>

                            <div class="flex justify-end gap-3">
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">Cancel</a>
                                <button class="px-4 py-2 rounded bg-orange-600 hover:bg-orange-700 text-white" type="submit">Change Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-navbar>
</x-layout>
