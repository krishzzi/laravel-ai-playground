<?php

namespace App\Livewire;

use App\Ai\Agents\ChatAgent;
use App\View\Components\Layouts\App;
use Laravel\Ai\Responses\StreamedAgentResponse;
use Livewire\Component;
use Livewire\WithFileUploads;

class ChatPage extends Component
{
    use WithFileUploads;

    public string $prompt = '';
    public array $attachments = [];
    public array $messages = [];
    public bool $isStreaming = false;

    public ?string $activeAssistantId = null;
    public ?string $activeQuestion = null;

    public function send()
    {
        $this->validate([
            'prompt' => ['required', 'string', 'max:4000'],
            'attachments.*' => ['nullable', 'file', 'max:10240'],
        ]);

        $prompt = trim($this->prompt);
        $attachments = $this->attachments;

        $userId = (string) str()->uuid();
        $assistantId = (string) str()->uuid();

        $this->messages[] = [
            'id' => $userId,
            'role' => 'user',
            'content' => $prompt,
            'attachments' => $attachments,
            'created_at' => now()->format('h:i A'),
        ];

        $this->messages[] = [
            'id' => $assistantId,
            'role' => 'assistant',
            'content' => 'Thinking...',
            'streaming' => true,
            'created_at' => now()->format('h:i A'),
        ];

        $this->activeAssistantId = $assistantId;
        $this->activeQuestion = $prompt;
        $this->isStreaming = true;

        $this->reset('prompt', 'attachments');

        $this->ask();
    }

    public function ask()
    {
        if (! $this->activeQuestion || ! $this->activeAssistantId) {
            return;
        }

        $agent = new ChatAgent();

        $stream = $agent->stream($this->activeQuestion);

        $stream->then(function (StreamedAgentResponse $response) {
            $text = trim((string) ($response->text ?? ''));

            foreach ($this->messages as &$message) {
                if (($message['id'] ?? null) === $this->activeAssistantId) {
                    $message['content'] = $text !== '' ? $text : 'No response received.';
                    $message['streaming'] = false;
                    break;
                }
            }

            $this->isStreaming = false;
            $this->activeAssistantId = null;
            $this->activeQuestion = null;
        });
    }

    public function render()
    {
        return view('livewire.chat-page', [
            'messages' => $this->messages,
        ])->layout(App::class);
    }
}
