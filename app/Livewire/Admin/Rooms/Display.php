<?php

namespace App\Livewire\Admin\Rooms;

use Livewire\Component;
use Kreait\Firebase\Contract\Database;
use Carbon\Carbon;

class Display extends Component
{
    public $search = '';
    public $perPage = 10;
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $page = 1;

    public $statusFilter = 'all';
    public $showFilterDropdown = false;
    public $dateFrom = '';
    public $dateTo = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'page' => ['except' => 1],
        'perPage' => ['except' => 10],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'statusFilter' => ['except' => 'all'],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    protected $listeners = ['refreshRooms' => '$refresh'];

    public function mount()
    {
        $this->search = request()->input('search', '');
        $this->page = request()->input('page', 1);

        $this->statusFilter = (string) request()->input('statusFilter', 'all');
        $this->dateFrom = (string) request()->input('dateFrom', '');
        $this->dateTo = (string) request()->input('dateTo', '');

        if (!in_array($this->statusFilter, ['all', 'normal', 'warning', 'urgent'], true)) {
            $this->statusFilter = 'all';
        }
    }

    public function updatingSearch(): void
    {
        $this->page = 1;
    }

    public function updatingPerPage(): void
    {
        $this->page = 1;
    }

    public function updatingStatusFilter(): void
    {
        $this->page = 1;
    }

    public function updatingDateFrom(): void
    {
        if ($this->dateFrom && $this->dateTo && $this->dateFrom > $this->dateTo) {
            $this->dateTo = '';
        }
        $this->page = 1;
    }

    public function updatingDateTo(): void
    {
        if ($this->dateTo && $this->dateFrom && $this->dateTo < $this->dateFrom) {
            $this->dateTo = '';
        }
        $this->page = 1;
    }

    public function toggleFilterDropdown(): void
    {
        $this->showFilterDropdown = !$this->showFilterDropdown;
    }

    public function resetFilters(): void
    {
        $this->statusFilter = 'all';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->page = 1;
        $this->showFilterDropdown = false;
    }

    public function quickFilterTodayStatus(string $status): void
    {
        $status = strtolower(trim($status));
        if (!in_array($status, ['urgent', 'warning'], true)) return;

        $today = Carbon::today()->format('Y-m-d');
        $isActive = $this->statusFilter === $status && $this->dateFrom === $today && $this->dateTo === $today;

        if ($isActive) {
            $this->statusFilter = 'all';
            $this->dateFrom = '';
            $this->dateTo = '';
        } else {
            $this->statusFilter = $status;
            $this->dateFrom = $today;
            $this->dateTo = $today;
        }

        $this->page = 1;
        $this->showFilterDropdown = false;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->page = 1;
    }

    public function toggleDoor(string $roomId): void
    {
        $snapshot = app(Database::class)->getReference('rooms/'.$roomId)->getSnapshot();
        $data = $snapshot->getValue();

        $current = 'closed';
        if (is_array($data) && isset($data['door_status'])) {
            $s = strtolower(trim((string) $data['door_status']));
            if (in_array($s, ['open', 'closed'], true)) {
                $current = $s;
            }
        }

        $next = $current === 'open' ? 'closed' : 'open';

        app(Database::class)->getReference('rooms/'.$roomId)->update([
            'door_status' => $next,
            'updated_at' => now()->toISOString(),
        ]);

        $this->dispatch('showToast', message: 'Door status updated.', type: 'success');
        $this->dispatch('refreshRooms');
    }

    public function resetResponse(string $roomId): void
    {
        app(Database::class)->getReference('rooms/'.$roomId)->update([
            'response' => 'no response',
            'updated_at' => now()->toISOString(),
        ]);

        $this->dispatch('showToast', message: 'Response reset.', type: 'success');
        $this->dispatch('refreshRooms');
    }

    protected function getRoomsFromFirebase(): array
    {
        $snapshot = app(Database::class)->getReference('rooms')->getSnapshot();
        $raw = $snapshot->getValue() ?? [];

        $rooms = [];
        foreach ($raw as $id => $data) {
            if (!is_array($data)) continue;

            $flame = $data['flame'] ?? false;
            $gas = $data['gas'] ?? 0;

            if (is_string($flame)) {
                $f = strtolower(trim($flame));
                if (in_array($f, ['1', 'true', 'yes', 'y', 'on'], true)) {
                    $flame = true;
                } elseif (in_array($f, ['0', 'false', 'no', 'n', 'off'], true)) {
                    $flame = false;
                }
            }
            if (is_numeric($flame)) {
                $flame = ((int) $flame) === 1;
            }
            if (!is_bool($flame)) {
                $flame = false;
            }

            if (is_string($gas) && strtolower(trim($gas)) === 'n/a') {
                $gas = 0;
            }

            $doorStatus = strtolower(trim((string) ($data['door_status'] ?? 'closed')));
            if (!in_array($doorStatus, ['open', 'closed'], true)) {
                $doorStatus = 'closed';
            }

            $motion = $data['motion'] ?? false;
            if (is_string($motion)) {
                $m = strtolower(trim($motion));
                if (in_array($m, ['1', 'true', 'yes', 'y', 'on'], true)) {
                    $motion = true;
                } elseif (in_array($m, ['0', 'false', 'no', 'n', 'off'], true)) {
                    $motion = false;
                }
            }
            if (is_numeric($motion)) {
                $motion = ((int) $motion) === 1;
            }
            if (!is_bool($motion)) {
                $motion = false;
            }

            $response = strtolower(trim((string) ($data['response'] ?? 'no response')));
            if (!in_array($response, ['yes', 'no', 'no response'], true)) {
                $response = 'no response';
            }

            $rooms[] = [
                'id' => $id,
                'name' => $data['name'] ?? '',
                'room_number' => $data['room_number'] ?? null,
                'flame' => $flame,
                'gas' => is_numeric($gas) ? (float) $gas : 0,
                'door_status' => $doorStatus,
                'motion' => $motion,
                'response' => $response,
                'created_at' => $data['created_at'] ?? '',
                'emergency_level' => $data['emergency_level'] ?? null,
                'last_emergency_at' => $data['last_emergency_at'] ?? null,
            ];
        }

        foreach ($rooms as &$r) {
            $lvl = strtolower((string) ($r['emergency_level'] ?? ''));
            if ($lvl === 'urgent') {
                $r['status'] = 'Urgent';
                $r['status_key'] = 'urgent';
            } elseif ($lvl === 'warning') {
                $r['status'] = 'Warning';
                $r['status_key'] = 'warning';
            } else {
                $r['status'] = 'Normal';
                $r['status_key'] = 'normal';
            }
        }
        unset($r);

        // Filter by search
        if ($this->search) {
            $rooms = array_filter($rooms, fn($r) => stripos($r['name'], $this->search) !== false);
        }

        if ($this->statusFilter !== 'all') {
            $want = strtolower($this->statusFilter);
            $rooms = array_filter($rooms, fn($r) => (($r['status_key'] ?? 'normal') === $want));
        }

        if ($this->dateFrom !== '' || $this->dateTo !== '') {
            $from = $this->dateFrom !== '' ? Carbon::parse($this->dateFrom)->startOfDay() : null;
            $to = $this->dateTo !== '' ? Carbon::parse($this->dateTo)->endOfDay() : null;

            $rooms = array_filter($rooms, function ($r) use ($from, $to) {
                $rawTs = (string) (($r['last_emergency_at'] ?? $r['created_at']) ?? '');
                if ($rawTs === '') return false;
                try {
                    $ts = Carbon::parse($rawTs);
                } catch (\Exception $e) {
                    return false;
                }

                if ($from && $ts->lt($from)) return false;
                if ($to && $ts->gt($to)) return false;
                return true;
            });
        }

        // Sort
        $numericFields = ['room_number', 'gas'];
        $booleanFields = ['flame', 'motion'];
        usort($rooms, function ($a, $b) use ($numericFields, $booleanFields) {
            $av = $a[$this->sortField] ?? '';
            $bv = $b[$this->sortField] ?? '';

            if (in_array($this->sortField, $numericFields, true)) {
                $av = is_numeric($av) ? (float) $av : 0;
                $bv = is_numeric($bv) ? (float) $bv : 0;
                $cmp = $av <=> $bv;
            } elseif (in_array($this->sortField, $booleanFields, true)) {
                $av = (bool) $av;
                $bv = (bool) $bv;
                $cmp = ((int) $av) <=> ((int) $bv);
            } else {
                $cmp = strcasecmp((string) $av, (string) $bv);
            }

            return $this->sortDirection === 'asc' ? $cmp : -$cmp;
        });

        return array_values($rooms);
    }

    public function getPaginationData(): array
    {
        $rooms = $this->getRoomsFromFirebase();
        $total = count($rooms);
        $lastPage = (int) ceil($total / $this->perPage);
        $currentPage = max(1, min($this->page, $lastPage ?: 1));

        $offset = ($currentPage - 1) * $this->perPage;
        $paged = array_slice($rooms, $offset, $this->perPage);

        // Simple pagination UI: show up to 3 pages
        if ($lastPage <= 3) {
            $startPage = 1;
            $endPage = $lastPage;
        } elseif ($currentPage == 1) {
            $startPage = 1;
            $endPage = min(3, $lastPage);
        } elseif ($currentPage == $lastPage) {
            $startPage = max(1, $lastPage - 2);
            $endPage = $lastPage;
        } else {
            $startPage = max(1, $currentPage - 1);
            $endPage = min($lastPage, $currentPage + 1);
        }
        $pages = range($startPage, $endPage);

        return [
            'rooms' => $paged,
            'pages' => $pages,
            'currentPage' => $currentPage,
            'lastPage' => $lastPage,
            'total' => $total,
            'search' => $this->search,
            'perPage' => $this->perPage,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'statusFilter' => $this->statusFilter,
            'showFilterDropdown' => $this->showFilterDropdown,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
        ];
    }

    public function gotoPage($page)
    {
        $page = (int) $page;
        $data = $this->getPaginationData();
        $this->page = max(1, min($page, $data['lastPage'] ?: 1));
    }

    public function render()
    {
        return view('livewire.admin.rooms.display', $this->getPaginationData());
    }
}
