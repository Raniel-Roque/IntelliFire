<x-layout>
    <x-navbar title="Rooms Management" :includeSidebar="true" :user="Auth::user()">
        <div class="container mx-auto px-4 pb-8 pt-4" data-firebase-emergency-listener>
            <livewire:admin.rooms.display />
        </div>
    </x-navbar>
</x-layout>