@props([
    'user' => null,
    'currentPage' => null
])

@php
    $sidebarItems = [
        [
            'label' => 'Dashboard',
            'href' => route('dashboard'),
            'active' => 'dashboard*'
        ],
    ];

    function hasActiveChild($item) {
        return false;
    }
@endphp

<div x-data="{ 
    isOpen: false,
    isCollapsed: localStorage.getItem('sidebar-collapsed') !== 'false',
    openDropdowns: (() => JSON.parse(localStorage.getItem('sidebar-dropdowns') || '{}'))(),
    toggleSidebar() {
        this.isCollapsed = !this.isCollapsed;
        localStorage.setItem('sidebar-collapsed', this.isCollapsed);
    },
    toggleMobile() {
        this.isOpen = !this.isOpen;
    },
    closeMobile() {
        this.isOpen = false;
    }
}" 
@toggle-sidebar.window="toggleSidebar()"
@toggle-mobile.window="toggleMobile()"
class="relative h-screen"
x-cloak>

    <div x-show="isOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-600/75 dark:bg-gray-900/75 z-40 lg:hidden"
         @click="closeMobile()">
    </div>

    <aside :class="[
        'fixed inset-y-0 left-0 z-50 flex flex-col bg-white dark:bg-gray-800 shadow-xl transform transition-all duration-300 ease-in-out lg:relative lg:translate-x-0',
        isOpen ? 'translate-x-0' : '-translate-x-full',
        isCollapsed ? 'lg:w-16' : 'lg:w-64',
        'w-64'
    ]" 
    class="h-screen overflow-hidden"
    @click.stop>

        <div class="flex items-center h-16 px-4 border-b border-gray-200 dark:border-gray-700 shrink-0 bg-white dark:bg-gray-800">
            <div class="flex items-center flex-1 lg:hidden">
                <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <span class="ml-3 text-xl font-bold text-gray-900 dark:text-gray-100">IntelliFire</span>
            </div>

            <div x-show="!isCollapsed" 
                 x-transition:enter="transition ease-in-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 class="hidden lg:flex items-center flex-1">
                <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <span class="ml-3 text-xl font-bold text-gray-900 dark:text-gray-100">IntelliFire</span>
            </div>

            <button @click="closeMobile()" 
                    class="lg:hidden ml-auto p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div x-show="isCollapsed" class="hidden lg:flex items-center justify-center w-full">
                <button @click="toggleSidebar()" 
                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

            <button x-show="!isCollapsed" @click="toggleSidebar()" 
                    class="hidden lg:block ml-auto p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                </svg>
            </button>
        </div>

        <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto bg-white dark:bg-gray-800" x-cloak>
            @foreach($sidebarItems as $item)
                <a href="{{ $item['href'] }}"
                   class="group flex items-center w-full px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200"
                   :class="{
                       'bg-orange-100 text-orange-700 shadow-sm': {{ request()->is($item['active']) ? 'true' : 'false' }},
                       'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white': {{ request()->is($item['active']) ? 'false' : 'true' }}
                   }">
                    <div class="shrink-0 w-6 h-6 flex items-center justify-center" :class="isCollapsed ? 'lg:mx-auto' : ''">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <span x-show="!isCollapsed" class="ml-3" x-cloak>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </aside>
</div>
