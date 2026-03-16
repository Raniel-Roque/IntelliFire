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
            $database = app(Database::class);

            // Determine next room_number
            $snapshot = $database->getReference('rooms')->getSnapshot();
            $raw = $snapshot->getValue() ?? [];
            $maxRoomNumber = 0;
            foreach ($raw as $data) {
                if (is_array($data) && isset($data['room_number']) && is_numeric($data['room_number'])) {
                    $maxRoomNumber = max($maxRoomNumber, (int)$data['room_number']);
                }
            }
            $nextRoomNumber = $maxRoomNumber + 1;

            $roomName = trim($this->name);

            $newRef = $database->getReference('rooms')->push([
                'name' => $roomName,
                'room_number' => $nextRoomNumber,
                'door_status' => 'closed',
                'motion' => false,
                'response' => 'no response',
                'created_at' => now()->toISOString(),
            ]);

            $roomId = $newRef->getKey();
            if (is_string($roomId) && $roomId !== '') {
                $database->getReference('room_users/'.$roomId)->set([
                    'username' => $roomName,
                    'password' => 'password',
                    'room_id' => $roomId,
                    'created_at' => now()->toISOString(),
                ]);
            }

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
