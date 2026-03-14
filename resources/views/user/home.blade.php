<x-layout>
    <x-navbar title="User Home" :includeSidebar="false" :user="auth('room')->user()">
        <div class="min-h-screen flex items-center justify-center px-4" x-data="{ doorConfirm: false, okModal: false }">
            <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h1 class="text-xl font-semibold mb-6 text-gray-900 dark:text-gray-100">User Home</h1>

                <div class="space-y-3">
                    <button type="button" @click="doorConfirm = true" class="w-full px-4 py-3 rounded bg-orange-600 hover:bg-orange-700 text-white font-medium">
                        {{ (($doorStatus ?? 'closed') === 'open') ? 'Close Door' : 'Open Door' }}
                    </button>

                    @if (($isUrgent ?? false) === true)
                        <button type="button" @click="okModal = true" class="w-full px-4 py-3 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 font-medium hover:bg-gray-50 dark:hover:bg-gray-600">
                            Are you okay?
                        </button>
                    @endif
                </div>
            </div>

            <div x-show="doorConfirm" x-cloak class="fixed inset-0 z-50" aria-modal="true" role="dialog">
                <div class="fixed inset-0 bg-black/50" @click="doorConfirm = false"></div>
                <div class="relative min-h-screen flex items-center justify-center p-4">
                    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Door Control</h2>
                        <p class="mt-2 text-gray-700 dark:text-gray-300">Are you sure you want to {{ (($doorStatus ?? 'closed') === 'open') ? 'close' : 'open' }} the door?</p>

                        <div class="mt-6 flex gap-3 justify-end">
                            <button type="button" @click="doorConfirm = false" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                                Cancel
                            </button>
                            <form method="POST" action="{{ route('user.door.toggle') }}">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                                    Yes, {{ (($doorStatus ?? 'closed') === 'open') ? 'close' : 'open' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @if (($isUrgent ?? false) === true)
                <div x-show="okModal" x-cloak class="fixed inset-0 z-50" aria-modal="true" role="dialog">
                    <div class="fixed inset-0 bg-black/50" @click="okModal = false"></div>
                    <div class="relative min-h-screen flex items-center justify-center p-4">
                        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Are you okay?</h2>
                            <p class="mt-2 text-gray-700 dark:text-gray-300">Please confirm your status.</p>

                            <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-end">
                                <button type="button" @click="okModal = false" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    Cancel
                                </button>
                                <form method="POST" action="{{ route('user.response') }}">
                                    @csrf
                                    <input type="hidden" name="response" value="yes">
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                        I'm OK
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('user.response') }}">
                                    @csrf
                                    <input type="hidden" name="response" value="no">
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                        Not OK
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </x-navbar>
</x-layout>
