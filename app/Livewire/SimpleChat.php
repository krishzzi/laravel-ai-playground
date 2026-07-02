<?php

namespace App\Livewire;

use App\Ai\Agents\OllamaAgent;
use App\Services\AiService\Phasers\InferencePhaser;
use App\View\Components\Layouts\Chat;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Laravel\Ai\Files\Image;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SimpleChat extends Component
{
    use WithFileUploads;


    public array $conversations = [];
    public string $prompt = '';
    public array $attachments = [];

    public function mount(): void
    {
        $this->conversations = [];
    }

    public function removeAttachment($index): void
    {
        if (! isset($this->attachments[$index])) {
            return;
        }

        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);
    }

    public function useQuickPrompt(string $text): void
    {
        $this->prompt = $text;
    }

    public function sendQuickPrompt(string $text): void
    {
        $this->prompt = $text;
        $this->ask();
    }



    protected function getSupportedAttachments(): array
    {
        return collect($this->attachments)
            ->filter(function ($file) {

                if (! $file instanceof TemporaryUploadedFile) {
                    return false;
                }

                return in_array(
                    Str::lower($file->getClientOriginalExtension()),
                    [
                        'jpg',
                        'jpeg',
                        'png',
                        'gif',
                        'webp',
                        'bmp',
                        'tif',
                        'tiff',
                    ],
                    true
                );

            })
            ->map(function (TemporaryUploadedFile $file) {

                // Store temporarily so Laravel AI receives a real image file.
                $path = $file->store('ai-images');

                return Image::fromStorage($path);

            })
            ->values()
            ->all();
    }


    public function ask()
    {
        if (blank($this->prompt)) {
            return;
        }

        $userPrompt = $this->prompt;
        $this->prompt = '';

        // Add user message immediately
        $this->conversations[] = [
            'role' => 'user',
            'content' => $userPrompt,
            'created_at' => now(),
        ];

        $attachments = $this->getSupportedAttachments();
        $this->attachments = [];
        $response = (string) (new OllamaAgent())
            ->prompt($userPrompt, attachments: $attachments)
            ->withMessages(collect($this->conversations));
        $response = InferencePhaser::make($response)->phrase();

        // Add assistant message
        $this->conversations[] = [
            'role' => 'assistant',
            'content' => $response,
            'created_at' => now(),
        ];
    }


    public function editPrompt($index)
    {
        $this->prompt = $this->conversations[$index]['content'];

        // remove everything after this prompt
        $this->conversations = array_slice(
            $this->conversations,
            0,
            $index
        );
    }



    public function regenerate()
    {
        if (count($this->conversations) < 2) {
            return;
        }

        // remove last assistant reply
        $assistant = array_pop($this->conversations);

        // previous must be user
        $user = end($this->conversations);

        $response = (string) (new OllamaAgent())
            ->prompt($user['content'])
            ->withMessages(collect($this->conversations));
        $response = InferencePhaser::make($response)->phrase();

        $this->conversations[] = [
            'role' => 'assistant',
            'content' => $response,
            'created_at' => now(),
        ];
    }

    public function getLastUserIndexProperty()
    {
        foreach (array_reverse($this->conversations, true) as $i => $msg) {
            if ($msg['role'] === 'user') {
                return $i;
            }
        }

        return null;
    }

    protected function getContext(): array
    {
        return collect($this->conversations)
            ->sortByDesc('created_at')
            ->take(5)
            ->values()
            ->all();
    }

    public function render()
    {
        return view('livewire.simple-chat')->layout(Chat::class);
    }
}
