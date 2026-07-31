<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use Illuminate\Http\Request;

class ChatSessionController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->chatSessions()
            ->where('archived', false)
            ->orderByDesc('pinned')->orderByDesc('last_activity_at')
            ->paginate(30);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'title' => 'nullable|string|max:120',
            'default_provider' => 'nullable|string',
            'default_model' => 'nullable|string',
            'thinking_mode' => 'nullable|in:instant,balanced,deep,max',
        ]);

        return $request->user()->chatSessions()->create($data + ['title' => $data['title'] ?? 'New Chat']);
    }

    public function show(Request $request, ChatSession $chatSession)
    {
        $this->authorizeOwner($request, $chatSession);

        return $chatSession->load('messages.artifacts');
    }

    public function update(Request $request, ChatSession $chatSession)
    {
        $this->authorizeOwner($request, $chatSession);

        $chatSession->update($request->validate([
            'title' => 'sometimes|string|max:120',
            'project_id' => 'sometimes|nullable|exists:projects,id',
            'pinned' => 'sometimes|boolean',
            'archived' => 'sometimes|boolean',
            'thinking_mode' => 'sometimes|in:instant,balanced,deep,max',
            'default_provider' => 'sometimes|string',
            'default_model' => 'sometimes|string',
        ]));

        return $chatSession;
    }

    public function destroy(Request $request, ChatSession $chatSession)
    {
        $this->authorizeOwner($request, $chatSession);
        $chatSession->delete();

        return response()->noContent();
    }

    protected function authorizeOwner(Request $request, ChatSession $session): void
    {
        abort_unless($session->user_id === $request->user()->id, 403);
    }
}
