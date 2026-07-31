<?php

namespace App\Livewire\Tools;

use App\Models\Tool;
use Livewire\Component;

class ToolsGallery extends Component
{
    public string $search = '';

    public bool $showCreateModal = false;

    public string $newSlug = '';

    public string $newName = '';

    public string $newDescription = '';

    public string $newPromptTemplate = '';

    public function create(): void
    {
        $this->validate([
            'newSlug' => 'required|alpha_dash|max:40|unique:tools,slug',
            'newName' => 'required|string|max:80',
            'newDescription' => 'required|string|max:300',
            'newPromptTemplate' => 'nullable|string|max:4000',
        ]);

        auth()->user()->tools()->create([
            'slug' => $this->newSlug,
            'name' => $this->newName,
            'description' => $this->newDescription,
            'prompt_template' => $this->newPromptTemplate,
            'kind' => 'prompt',
            'is_public' => false,
        ]);

        $this->reset(['newSlug', 'newName', 'newDescription', 'newPromptTemplate', 'showCreateModal']);
    }

    public function toggleActive(Tool $tool): void
    {
        abort_unless($tool->user_id === auth()->id(), 403);
        $tool->update(['is_active' => ! $tool->is_active]);
    }

    public function render()
    {
        $tools = Tool::query()->availableTo(auth()->user())
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderByDesc('is_public')
            ->get();

        return view('livewire.tools.tools-gallery', ['tools' => $tools]);
    }
}
