<?php

namespace App\Livewire\Admin\Rooms;

use Livewire\Component;
use Kreait\Firebase\Contract\Database;

class Edit extends Component
{
    public $roomId;
    public $name = '';
    public $showModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    protected $messages = [
        'name.required' => 'Room name is required.',
        'name.max' => 'Room name must not exceed 255 characters.',
    ];

    protected $listeners = ['openEditModal' => 'openModal'];

    public function openModal($roomId)
    {
        $this->roomId = $roomId;
        $snapshot = app(Database::class)->getReference('rooms/'.$roomId)->getSnapshot();
        $data = $snapshot->getValue();
        if (is_array($data)) {
            $this->name = $data['name'] ?? '';
        }
        $this->resetValidation();
        $this->showModal = true;
    }

    public function updateRoom()
    {
        $this->validate();

        try {
            app(Database::class)->getReference('rooms/'.$this->roomId)->update([
                'name' => trim($this->name),
                'updated_at' => now()->toISOString(),
            ]);

            $roomName = $this->name;
            $this->closeModal();
            $this->dispatch('refreshRooms');
            $this->dispatch('showToast', message: "{$roomName} has been updated!", type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('showToast', message: 'Failed to update room. Please try again.', type: 'error');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['name', 'roomId']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.rooms.edit');
    }
}
