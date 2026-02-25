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
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="current_password">Current Password</label>
                                <input class="mt-1 w-full border rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600" id="current_password" name="current_password" type="password" autocomplete="current-password" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="new_password">New Password</label>
                                <input class="mt-1 w-full border rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600" id="new_password" name="new_password" type="password" autocomplete="new-password" minlength="8" required>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Minimum 8 characters</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="new_password_confirmation">Confirm New Password</label>
                                <input class="mt-1 w-full border rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600" id="new_password_confirmation" name="new_password_confirmation" type="password" autocomplete="new-password" minlength="8" required>
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
