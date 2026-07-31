<?php

namespace App\Http\Controllers\Api\V1;

use App\Ai\Agents\ChatAgent;
use App\Exceptions\InsufficientGemsException;
use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\Skill;
use App\Models\Tool;
use App\Services\MentionParser;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    public function index(Request $request, ChatSession $chatSession)
    {
        $this->authorizeOwner($request, $chatSession);

        return $chatSession->messages()->with('artifacts')->paginate(50);
    }

    public function store(Request $request, ChatSession $chatSession, MentionParser $parser)
    {
        $this->authorizeOwner($request, $chatSession);

        $data = $request->validate([
            'content' => 'required|string|max:32000',
            'attachments' => 'nullable|array',
        ]);

        $parsed = $parser->parse($data['content'], $request->user());

        $chatSession->messages()->create([
            'role' => 'user',
            'content' => $parsed['prompt'],
            'attachments' => $data['attachments'] ?? null,
        ]);

        try {
            $agent = new ChatAgent($chatSession, $parsed['tools'], $parsed['skills']);
            $response = $agent->prompt('', provider: $chatSession->default_provider, model: $chatSession->default_model);
        } catch (InsufficientGemsException $e) {
            return response()->json(['error' => $e->getMessage()], 402);
        }

        $assistantMessage = $chatSession->messages()->create([
            'role' => 'assistant',
            'content' => (string) $response,
            'provider' => $chatSession->default_provider,
            'model' => $chatSession->default_model,
            'thinking_mode' => $chatSession->thinking_mode,
            'input_tokens' => $response->usage->inputTokens ?? null,
            'output_tokens' => $response->usage->outputTokens ?? null,
        ]);

        $chatSession->touchActivity();

        return $assistantMessage;
    }

    public function branch(Request $request, ChatSession $chatSession, $message)
    {
        $this->authorizeOwner($request, $chatSession);

        $data = $request->validate(['content' => 'required|string|max:32000']);

        $original = $chatSession->messages()->findOrFail($message);
        $chatSession->messages()->where('created_at', '>', $original->created_at)->delete();
        $original->update(['content' => $data['content'], 'edited' => true]);

        return response()->json(['status' => 'branched', 'message_id' => $original->id]);
    }

    protected function authorizeOwner(Request $request, ChatSession $session): void
    {
        abort_unless($session->user_id === $request->user()->id, 403);
    }
}
