<x-layout>
    <x-navbar title="Dashboard" :includeSidebar="true" :user="auth()->user()">
        <div class="container mx-auto px-4 pb-8 pt-4">
            <div class="max-w-3xl mx-auto">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Dashboard</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Signed in as {{ auth()->user()->email }}</p>
                </div>

                <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <p class="text-sm text-gray-700 dark:text-gray-300">Next: we will show device status and fire events here.</p>
                </div>
            </div>
        </div>
    </x-navbar>
</x-layout>
