<?php

namespace App\Livewire\Admin\Rooms;

use Livewire\Component;
use Kreait\Firebase\Contract\Database;

class Delete extends Component
{
    public $roomId;
    public $roomName = '';
    public $showModal = false;

    protected $listeners = ['openDeleteModal' => 'openModal'];

    public function openModal($roomId)
    {
        $this->roomId = $roomId;
        $snapshot = app(Database::class)->getReference('rooms/'.$roomId)->getSnapshot();
        $data = $snapshot->getValue();
        if (is_array($data)) {
            $this->roomName = $data['name'] ?? '';
        }
        $this->showModal = true;
    }

    public function deleteRoom()
    {
        try {
            app(Database::class)->getReference('rooms/'.$this->roomId)->remove();

            $roomName = $this->roomName;
            $this->closeModal();
            $this->dispatch('refreshRooms');
            $this->dispatch('showToast', message: "{$roomName} has been deleted.", type: 'info');
        } catch (\Exception $e) {
            $this->dispatch('showToast', message: 'Failed to delete room. Please try again.', type: 'error');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['roomId', 'roomName']);
    }

    public function render()
    {
        return view('livewire.admin.rooms.delete');
    }
}
