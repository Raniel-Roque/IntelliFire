<div>
    <div class="flex flex-col gap-4 mb-2 md:flex-row md:items-center md:justify-between md:gap-6">
        <div class="text-center md:text-left">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Notification Logs</h2>
            <p class="text-gray-600 dark:text-gray-400">Warnings and urgent events from devices.</p>
        </div>

        <div class="flex flex-col gap-3 md:flex-row md:gap-3 md:items-center">
            <div class="flex flex-row gap-3 items-center w-full md:w-auto">
                <div class="relative shrink-0 flex-1 md:flex-initial">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input
                        wire:model.live="search"
                        placeholder="Search room name..."
                        class="w-full md:w-72 pl-9 pr-12 py-2 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all placeholder:text-gray-400 dark:placeholder:text-gray-500"
                    />

                    <button type="button" wire:click="toggleFilterDropdown" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors cursor-pointer" title="Filters">
                        <svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="#9CA3AF" class="w-5 h-5">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M15 2v1.67l-5 4.759V14H6V8.429l-5-4.76V2h14zM7 8v5h2V8l5-4.76V3H2v.24L7 8z"/>
                        </svg>
                    </button>

                    @if ($showFilterDropdown)
                        <div class="absolute top-full right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                            <div class="p-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Status</h3>
                                        <div class="space-y-2">
                                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                                <input type="radio" wire:model.live="statusFilter" value="all" class="text-orange-600 focus:ring-orange-500">
                                                <span>All</span>
                                            </label>
                                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                                <input type="radio" wire:model.live="statusFilter" value="urgent" class="text-orange-600 focus:ring-orange-500">
                                                <span>Urgent</span>
                                            </label>
                                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                                <input type="radio" wire:model.live="statusFilter" value="warning" class="text-orange-600 focus:ring-orange-500">
                                                <span>Warning</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Date Range</h3>
                                        <div class="space-y-2">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">From</label>
                                                <input type="date" wire:model.live="dateFrom" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100" />
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">To</label>
                                                <input type="date" wire:model.live="dateTo" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-between mt-4 pt-3 border-t border-gray-200 dark:border-gray-700">
                                    <button type="button" wire:click="resetFilters" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">Reset</button>
                                    <button type="button" wire:click="toggleFilterDropdown" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">Done</button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-2 mb-6 md:flex-row md:items-center md:justify-between">
        <div class="flex flex-wrap gap-3">
            <div class="text-sm font-medium text-gray-700 dark:text-gray-300 self-center">Today:</div>
            <button
                type="button"
                wire:click="quickFilterTodayStatus('urgent')"
                class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors
                    {{ ($statusFilter === 'urgent' && $dateFrom === now()->format('Y-m-d') && $dateTo === now()->format('Y-m-d'))
                        ? 'bg-red-600 text-white hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-800' }}"
            >
                Urgent
            </button>
            <button
                type="button"
                wire:click="quickFilterTodayStatus('warning')"
                class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors
                    {{ ($statusFilter === 'warning' && $dateFrom === now()->format('Y-m-d') && $dateTo === now()->format('Y-m-d'))
                        ? 'bg-yellow-500 text-white hover:bg-yellow-600 dark:bg-yellow-600 dark:hover:bg-yellow-700'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-800' }}"
            >
                Warning
            </button>
        </div>

        <div class="text-xs text-gray-600 dark:text-gray-300">
            <span class="font-medium">Fire Dept:</span>
            <a href="tel:+639663501733" class="font-semibold text-gray-900 dark:text-gray-100 hover:underline">+63 966 350 1733</a>
        </div>
    </div>

    <div class="relative flex flex-col w-full h-full text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 shadow-md dark:shadow-lg rounded-lg bg-clip-border">
        <div class="overflow-x-auto">
            <table class="w-full text-left table-auto min-w-max">
                <thead>
                    <tr>
                        <th class="p-3 md:p-4 border-b border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-gray-700 cursor-pointer hover:bg-slate-100 dark:hover:bg-gray-600" wire:click="sortBy('created_at')">
                            <p class="text-xs md:text-sm font-semibold leading-none text-slate-700 dark:text-slate-200 flex items-center gap-1">
                                Date and Time
                                @if ($sortField === 'created_at')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if ($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        @endif
                                    </svg>
                                @endif
                            </p>
                        </th>
                        <th class="p-3 md:p-4 border-b border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-gray-700 cursor-pointer hover:bg-slate-100 dark:hover:bg-gray-600" wire:click="sortBy('room_name')">
                            <p class="text-xs md:text-sm font-semibold leading-none text-slate-700 dark:text-slate-200 flex items-center gap-1">
                                Room Name
                                @if ($sortField === 'room_name')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if ($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        @endif
                                    </svg>
                                @endif
                            </p>
                        </th>
                        <th class="p-3 md:p-4 border-b border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-gray-700 cursor-pointer hover:bg-slate-100 dark:hover:bg-gray-600" wire:click="sortBy('status')">
                            <p class="text-xs md:text-sm font-semibold leading-none text-slate-700 dark:text-slate-200 flex items-center gap-1">
                                Status
                                @if ($sortField === 'status')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if ($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        @endif
                                    </svg>
                                @endif
                            </p>
                        </th>
                        <th class="p-3 md:p-4 border-b border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-gray-700 cursor-pointer hover:bg-slate-100 dark:hover:bg-gray-600" wire:click="sortBy('temperature')">
                            <p class="text-xs md:text-sm font-semibold leading-none text-slate-700 dark:text-slate-200 flex items-center gap-1">
                                Temp
                                @if ($sortField === 'temperature')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if ($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        @endif
                                    </svg>
                                @endif
                            </p>
                        </th>
                        <th class="p-3 md:p-4 border-b border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-gray-700 cursor-pointer hover:bg-slate-100 dark:hover:bg-gray-600" wire:click="sortBy('gas')">
                            <p class="text-xs md:text-sm font-semibold leading-none text-slate-700 dark:text-slate-200 flex items-center gap-1">
                                Gas
                                @if ($sortField === 'gas')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if ($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        @endif
                                    </svg>
                                @endif
                            </p>
                        </th>
                        <th class="p-3 md:p-4 border-b border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-gray-700">
                            <p class="text-xs md:text-sm font-semibold leading-none text-slate-700 dark:text-slate-200">Description</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr class="even:bg-slate-50 dark:even:bg-gray-700/50 hover:bg-slate-100 dark:hover:bg-gray-700">
                            <td class="p-3 md:p-4 py-4 md:py-5">
                                <p class="block text-xs md:text-sm text-slate-800 dark:text-slate-200">{{ $log['created_at'] ? \Carbon\Carbon::parse($log['created_at'])->format('M d, Y h:i A') : '—' }}</p>
                            </td>
                            <td class="p-3 md:p-4 py-4 md:py-5">
                                <p class="block text-xs md:text-sm text-slate-800 dark:text-slate-200">{{ $log['room_name'] }}</p>
                            </td>
                            <td class="p-3 md:p-4 py-4 md:py-5">
                                @if (($log['status'] ?? '') === 'URGENT')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">URGENT</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">WARNING</span>
                                @endif
                            </td>
                            <td class="p-3 md:p-4 py-4 md:py-5">
                                <p class="block text-xs md:text-sm text-slate-800 dark:text-slate-200">{{ $log['temperature'] ?? 0 }}</p>
                            </td>
                            <td class="p-3 md:p-4 py-4 md:py-5">
                                <p class="block text-xs md:text-sm text-slate-800 dark:text-slate-200">{{ $log['gas'] ?? 0 }}</p>
                            </td>
                            <td class="p-3 md:p-4 py-4 md:py-5">
                                <p class="block text-xs md:text-sm text-slate-800 dark:text-slate-200">{{ $log['description'] }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-sm text-gray-600 dark:text-gray-300">
                                No notifications found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (($lastPage ?? 1) > 1)
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between p-4 border-t border-slate-300 dark:border-gray-700">
                <p class="text-xs text-gray-600 dark:text-gray-300">
                    Showing page {{ $currentPage }} of {{ $lastPage }} ({{ $total }} total)
                </p>

                <div class="flex items-center gap-2 justify-center md:justify-end">
                    <button type="button" wire:click="gotoPage(1)" class="px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700" @disabled($currentPage == 1)>
                        First
                    </button>
                    <button type="button" wire:click="gotoPage({{ $currentPage - 1 }})" class="px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700" @disabled($currentPage == 1)>
                        Prev
                    </button>

                    @foreach ($pages as $p)
                        <button type="button" wire:click="gotoPage({{ $p }})" class="px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 {{ $p == $currentPage ? 'bg-orange-600 text-white border-orange-600 hover:bg-orange-700' : '' }}">
                            {{ $p }}
                        </button>
                    @endforeach

                    <button type="button" wire:click="gotoPage({{ $currentPage + 1 }})" class="px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700" @disabled($currentPage == $lastPage)>
                        Next
                    </button>
                    <button type="button" wire:click="gotoPage({{ $lastPage }})" class="px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700" @disabled($currentPage == $lastPage)>
                        Last
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
