<?php

namespace App\Livewire\Admin\Notifications;

use Livewire\Component;
use Kreait\Firebase\Contract\Database;
use Carbon\Carbon;

class Logs extends Component
{
    public $perPage = 10;
    public $page = 1;
    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $statusFilter = 'all';
    public $showFilterDropdown = false;
    public $dateFrom = '';
    public $dateTo = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'page' => ['except' => 1],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'statusFilter' => ['except' => 'all'],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->page = (int) request()->input('page', 1);
        $this->search = (string) request()->input('search', '');
        $this->sortField = (string) request()->input('sortField', 'created_at');
        $this->sortDirection = (string) request()->input('sortDirection', 'desc');
        $this->statusFilter = (string) request()->input('statusFilter', 'all');
        $this->dateFrom = (string) request()->input('dateFrom', '');
        $this->dateTo = (string) request()->input('dateTo', '');

        $this->perPage = 10;
        if (!in_array($this->sortDirection, ['asc', 'desc'], true)) $this->sortDirection = 'desc';
        if (!in_array($this->statusFilter, ['all', 'urgent', 'warning'], true)) $this->statusFilter = 'all';
    }

    public function updatingSearch(): void
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

    public function sortBy($field): void
    {
        $allowed = ['created_at', 'room_name', 'gas', 'temperature', 'status'];
        if (!in_array($field, $allowed, true)) return;

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = $field === 'created_at' ? 'desc' : 'asc';
        }
        $this->page = 1;
    }

    protected function normalizeEntry(string $id, array $data): array
    {
        $level = strtolower((string) ($data['level'] ?? 'warning'));
        $status = $level === 'urgent' ? 'URGENT' : 'WARNING';

        $roomName = (string) ($data['room_name'] ?? '');
        if ($roomName === '') {
            $roomNumber = $data['room_number'] ?? null;
            $roomName = $roomNumber !== null ? ('Room '.$roomNumber) : '—';
        }

        $temperature = $data['temperature'] ?? 0;
        $gas = $data['gas'] ?? 0;

        if (is_string($temperature) && strtolower(trim($temperature)) === 'n/a') $temperature = 0;
        if (is_string($gas) && strtolower(trim($gas)) === 'n/a') $gas = 0;

        $description = (string) ($data['reason'] ?? '');
        if ($description === '') $description = (string) ($data['message'] ?? '');

        if ($description === '') {
            $title = (string) ($data['title'] ?? '');
            if ($title !== '') {
                $title = preg_replace('/^(WARNING|URGENT)\s*:\s*/i', '', $title) ?? $title;
                $title = preg_replace('/\s+in\s+.+$/i', '', $title) ?? $title;
                $description = trim($title);
            }
        }

        if ($description === '') $description = '—';

        return [
            'id' => $id,
            'created_at' => (string) ($data['created_at'] ?? ''),
            'room_name' => $roomName,
            'temperature' => is_numeric($temperature) ? (float) $temperature : 0,
            'gas' => is_numeric($gas) ? (float) $gas : 0,
            'status' => $status,
            'description' => $description,
        ];
    }

    protected function getLogsFromFirebase(): array
    {
        $snapshot = app(Database::class)->getReference('emergencies/log')->getSnapshot();
        $raw = $snapshot->getValue() ?? [];

        $items = [];
        if (is_array($raw)) {
            foreach ($raw as $id => $data) {
                if (!is_string($id)) continue;
                if (!is_array($data)) continue;
                $items[] = $this->normalizeEntry($id, $data);
            }
        }

        if ($this->search !== '') {
            $needle = mb_strtolower($this->search);
            $items = array_filter($items, function ($row) use ($needle) {
                $hay = mb_strtolower((string) ($row['room_name'] ?? ''));
                return str_contains($hay, $needle);
            });
        }

        if ($this->statusFilter !== 'all') {
            $want = strtoupper($this->statusFilter);
            $items = array_filter($items, fn($row) => strtoupper((string) ($row['status'] ?? '')) === $want);
        }

        if ($this->dateFrom !== '' || $this->dateTo !== '') {
            $from = $this->dateFrom !== '' ? Carbon::parse($this->dateFrom)->startOfDay() : null;
            $to = $this->dateTo !== '' ? Carbon::parse($this->dateTo)->endOfDay() : null;

            $items = array_filter($items, function ($row) use ($from, $to) {
                $rawTs = (string) ($row['created_at'] ?? '');
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

        $numericFields = ['gas', 'temperature'];
        usort($items, function ($a, $b) use ($numericFields) {
            $av = $a[$this->sortField] ?? '';
            $bv = $b[$this->sortField] ?? '';

            if (in_array($this->sortField, $numericFields, true)) {
                $av = is_numeric($av) ? (float) $av : 0;
                $bv = is_numeric($bv) ? (float) $bv : 0;
                $cmp = $av <=> $bv;
            } else {
                $cmp = strcasecmp((string) $av, (string) $bv);
            }

            return $this->sortDirection === 'asc' ? $cmp : -$cmp;
        });

        return array_values($items);
    }

    public function getPaginationData(): array
    {
        $logs = $this->getLogsFromFirebase();
        $total = count($logs);
        $lastPage = (int) ceil($total / $this->perPage);
        $currentPage = max(1, min((int) $this->page, $lastPage ?: 1));

        $offset = ($currentPage - 1) * $this->perPage;
        $paged = array_slice($logs, $offset, $this->perPage);

        $window = 10;
        if ($lastPage <= $window) {
            $startPage = 1;
            $endPage = $lastPage;
        } else {
            $half = (int) floor($window / 2);
            $startPage = max(1, $currentPage - $half);
            $endPage = $startPage + $window - 1;
            if ($endPage > $lastPage) {
                $endPage = $lastPage;
                $startPage = $endPage - $window + 1;
            }
        }

        return [
            'logs' => $paged,
            'pages' => $lastPage ? range($startPage, $endPage) : [],
            'currentPage' => $currentPage,
            'lastPage' => $lastPage,
            'total' => $total,
            'perPage' => $this->perPage,
            'search' => $this->search,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'statusFilter' => $this->statusFilter,
            'showFilterDropdown' => $this->showFilterDropdown,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
        ];
    }

    public function gotoPage($page): void
    {
        $page = (int) $page;
        $data = $this->getPaginationData();
        $this->page = max(1, min($page, $data['lastPage'] ?: 1));
    }

    public function render()
    {
        return view('livewire.admin.notifications.logs', $this->getPaginationData());
    }
}
