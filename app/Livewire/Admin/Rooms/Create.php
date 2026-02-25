<?php

namespace App\Livewire\Admin\Rooms;

use Livewire\Component;
use Kreait\Firebase\Contract\Database;

class Create extends Component
{
    public $name = '';
    public $showModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    protected $messages = [
        'name.required' => 'Room name is required.',
        'name.max' => 'Room name must not exceed 255 characters.',
    ];

    protected $listeners = ['openCreateModal' => 'openModal'];

    public function openModal()
    {
        $this->reset(['name']);
        $this->resetValidation();
        $this->showModal = true;
    }

    public function createRoom()
    {
        $this->validate();

        try {
            // Determine next room_number
            $snapshot = app(Database::class)->getReference('rooms')->getSnapshot();
            $raw = $snapshot->getValue() ?? [];
            $maxRoomNumber = 0;
            foreach ($raw as $data) {
                if (is_array($data) && isset($data['room_number']) && is_numeric($data['room_number'])) {
                    $maxRoomNumber = max($maxRoomNumber, (int)$data['room_number']);
                }
            }
            $nextRoomNumber = $maxRoomNumber + 1;

            $newRef = app(Database::class)->getReference('rooms')->push([
                'name' => trim($this->name),
                'room_number' => $nextRoomNumber,
                'created_at' => now()->toISOString(),
            ]);

            $roomName = $this->name;
            $this->closeModal();
            $this->dispatch('refreshRooms');
            $this->dispatch('showToast', message: "{$roomName} has been successfully added!", type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('showToast', message: 'Failed to add room. Please try again.', type: 'error');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['name']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.rooms.create');
    }
}
