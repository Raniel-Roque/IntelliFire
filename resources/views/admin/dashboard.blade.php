<x-layout>
    <x-navbar title="Dashboard" :includeSidebar="true" :user="auth()->user()">
        <div class="container mx-auto px-4 pb-8">
            <div class="max-w-7xl mx-auto">
                <div class="mt-6">
                    <livewire:admin.notifications.logs />
                </div>
            </div>
        </div>
    </x-navbar>

    <script>
        window.dispatchEvent(new CustomEvent('clearToasts'));
    </script>
</x-layout>
