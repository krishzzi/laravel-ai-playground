<?php

namespace App\Livewire\Studio;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

/**
 * NOTE: video generation is NOT part of the documented Laravel AI SDK
 * feature table (text/images/TTS/STT/embeddings/rerank/files only) — there
 * is no `Laravel\Ai\Video` class to wrap. This component is wired as a
 * plain HTTP-client integration point against whichever video provider you
 * choose (Runway, Luma, Kling, Pika, etc.), following the same gem-billing
 * and job-queue pattern as the rest of the studio so it drops in cleanly
 * once you pick a vendor and add its config to `services.php`.
 */
class VideoStudio extends Component
{
    public string $prompt = '';

    public string $provider = 'runway'; // placeholder — configure in config/services.php

    public array $jobs = []; // [{id, status, video_url, gems_charged}]

    public function generate(): void
    {
        $this->validate(['prompt' => 'required|string|max:2000']);

        // Example shape only — replace with the real vendor call once
        // chosen, then poll / webhook back into this component the same
        // way ImageAgent/VoiceAgent debit gems on completion.
        // $response = Http::withToken(config('services.runway.key'))
        //     ->post('https://api.runwayml.com/v1/generate', ['prompt' => $this->prompt]);

        $this->jobs[] = [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'status' => 'queued',
            'prompt' => $this->prompt,
        ];

        $this->prompt = '';
    }

    public function render()
    {
        return view('livewire.studio.video-studio');
    }
}
