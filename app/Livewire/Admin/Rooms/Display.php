<?php

namespace App\Livewire\Admin\Rooms;

use Livewire\Component;
use Kreait\Firebase\Contract\Database;

class Display extends Component
{
    public $search = '';
    public $perPage = 10;
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $page = 1;

    protected $queryString = [
        'search' => ['except' => ''],
        'page' => ['except' => 1],
        'perPage' => ['except' => 10],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    protected $listeners = ['refreshRooms' => '$refresh'];

    public function mount()
    {
        $this->search = request()->input('search', '');
        $this->page = request()->input('page', 1);
    }

    public function updatingSearch(): void
    {
        $this->page = 1;
    }

    public function updatingPerPage(): void
    {
        $this->page = 1;
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

    protected function getRoomsFromFirebase(): array
    {
        $snapshot = app(Database::class)->getReference('rooms')->getSnapshot();
        $raw = $snapshot->getValue() ?? [];

        $rooms = [];
        foreach ($raw as $id => $data) {
            if (!is_array($data)) continue;

            $temperature = $data['temperature'] ?? 0;
            $gas = $data['gas'] ?? 0;

            if (is_string($temperature) && strtolower(trim($temperature)) === 'n/a') {
                $temperature = 0;
            }
            if (is_string($gas) && strtolower(trim($gas)) === 'n/a') {
                $gas = 0;
            }

            $rooms[] = [
                'id' => $id,
                'name' => $data['name'] ?? '',
                'room_number' => $data['room_number'] ?? null,
                'temperature' => is_numeric($temperature) ? (float) $temperature : 0,
                'gas' => is_numeric($gas) ? (float) $gas : 0,
                'created_at' => $data['created_at'] ?? '',
            ];
        }

        // Filter by search
        if ($this->search) {
            $rooms = array_filter($rooms, fn($r) => stripos($r['name'], $this->search) !== false);
        }

        // Sort
        $numericFields = ['room_number', 'temperature', 'gas'];
        usort($rooms, function ($a, $b) use ($numericFields) {
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
