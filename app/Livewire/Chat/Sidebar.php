<?php

namespace App\Livewire\Chat;

use App\Models\ChatSession;
use App\Models\Project;
use Livewire\Attributes\On;
use Livewire\Component;

class Sidebar extends Component
{
    public string $search = '';

    public ?int $activeProjectId = null;

    public bool showNewProjectModal = false;

    public string $newProjectName = '';

    public function newChat(): void
    {
        $session = auth()->user()->chatSessions()->create([
            'project_id' => $this->activeProjectId,
            'title' => 'New Chat',
        ]);

        $this->redirectRoute('chat.show', $session, navigate: true);
    }

    public function pin(ChatSession $session): void
    {
        $this->authorize('update', $session);
        $session->update(['pinned' => ! $session->pinned]);
    }

    public function archive(ChatSession $session): void
    {
        $this->authorize('update', $session);
        $session->update(['archived' => true]);
    }

    public function moveToProject(ChatSession $session, ?int $projectId): void
    {
        $this->authorize('update', $session);
        $session->update(['project_id' => $projectId]);
    }

    public function createProject(): void
    {
        $this->validate(['newProjectName' => 'required|string|max:80']);

        $project = auth()->user()->projects()->create(['name' => $this->newProjectName]);

        $this->newProjectName = '';
        $this->showNewProjectModal = false;
        $this->activeProjectId = $project->id;
    }

    #[On('session-updated')]
    public function refreshList(): void {}

    public function render()
    {
        $sessions = auth()->user()->chatSessions()
            ->where('archived', false)
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->activeProjectId, fn ($q) => $q->where('project_id', $this->activeProjectId))
            ->orderByDesc('pinned')
            ->orderByDesc('last_activity_at')
            ->get();

        return view('livewire.chat.sidebar', [
            'sessions' => $sessions,
            'projects' => auth()->user()->projects()->withCount('chatSessions')->get(),
        ]);
    }
}
