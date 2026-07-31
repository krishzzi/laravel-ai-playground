<?php

namespace App\Livewire\Chat;

use App\Ai\Agents\ChatAgent;
use App\Exceptions\InsufficientGemsException;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Services\GemBillingService;
use App\Services\MentionParser;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatWindow extends Component
{
    public ChatSession $session;

    public string $draft = '';

    public array $pendingAttachments = []; // browser-ingested {name, mime, extracted_text, preview_url}

    public bool $isStreaming = false;

    public ?string $splitViewArtifactUuid = null;

    public string $thinkingMode = 'balanced';

    public string $provider = 'anthropic';

    public string $model = 'claude-sonnet-5';

    public function mount(ChatSession $session): void
    {
        $this->authorizeSession($session);

        $this->session = $session;
        $this->thinkingMode = $session->thinking_mode;
        $this->provider = $session->default_provider ?? 'anthropic';
        $this->model = $session->default_model ?? 'claude-sonnet-5';
    }

    protected function authorizeSession(ChatSession $session): void
    {
        abort_unless($session->user_id === auth()->id(), 403);
    }

    /**
     * Called by the composer's JS once client-side ingestion (PDF.js /
     * mammoth / vision-ready base64, all done in-browser) has produced a
     * lightweight text/embedding representation of an attached file — we
     * never receive or store the raw file server-side unless the user
     * explicitly saves it to their library.
     */
    #[On('file-ingested')]
    public function attachIngestedFile(array $file): void
    {
        $this->pendingAttachments[] = $file;
    }

    public function removeAttachment(int $index): void
    {
        unset($this->pendingAttachments[$index]);
        $this->pendingAttachments = array_values($this->pendingAttachments);
    }

    public function send(MentionParser $parser, GemBillingService $billing): void
    {
        $this->validate(['draft' => 'required|string|min:1|max:32000']);

        $parsed = $parser->parse($this->draft, auth()->user());

        $userMessage = $this->session->messages()->create([
            'role' => 'user',
            'content' => $parsed['prompt'],
            'attachments' => $this->pendingAttachments ?: null,
        ]);

        $this->session->update([
            'default_provider' => $this->provider,
            'default_model' => $this->model,
            'thinking_mode' => $this->thinkingMode,
        ]);

        if ($this->session->messages()->count() === 1) {
            $this->session->update(['title' => $this->session->autoTitleFrom($this->draft)]);
        }

        $this->draft = '';
        $this->pendingAttachments = [];
        $this->isStreaming = true;

        $this->dispatch('message-sent', messageId: $userMessage->id, tools: $parsed['tools']->pluck('slug'), skills: $parsed['skills']->pluck('slug'));

        // The actual generation is kicked off from JS against the streaming
        // endpoint (routes/web.php: POST /chat/{session}/stream) so tokens
        // can be rendered via SSE without blocking this Livewire request.
        $this->dispatch('start-stream', sessionId: $this->session->uuid, messageId: $userMessage->id);
    }

    #[On('stream-complete')]
    public function onStreamComplete(array $payload): void
    {
        $this->isStreaming = false;
        $this->session->touchActivity();
    }

    #[On('stream-error')]
    public function onStreamError(string $error): void
    {
        $this->isStreaming = false;
        $this->addError('stream', $error);
    }

    public function editAndBranch(int $messageId, string $newContent): void
    {
        $original = ChatMessage::findOrFail($messageId);
        abort_unless($original->chat_session_id === $this->session->id, 403);

        // Everything after this message in the branch is superseded, not
        // deleted — soft "fork" behavior like Claude/ChatGPT edit+regenerate.
        $this->session->messages()
            ->where('created_at', '>', $original->created_at)
            ->delete();

        $original->update(['content' => $newContent, 'edited' => true]);

        $this->dispatch('start-stream', sessionId: $this->session->uuid, messageId: $original->id);
    }

    public function openInSplitView(string $artifactUuid): void
    {
        $this->splitViewArtifactUuid = $artifactUuid;
    }

    public function closeSplitView(): void
    {
        $this->splitViewArtifactUuid = null;
    }

    public function render()
    {
        return view('livewire.chat.chat-window', [
            'messages' => $this->session->messages()->with('artifacts')->get(),
            'gemsBalance' => app(GemBillingService::class)->balance(auth()->user()),
        ]);
    }
}
