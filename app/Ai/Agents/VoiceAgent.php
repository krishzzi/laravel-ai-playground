<?php

namespace App\Ai\Agents;

use App\Models\User;
use App\Services\GemBillingService;
use App\Services\ModelCatalogService;
use App\Services\ProviderResolver;
use Illuminate\Http\UploadedFile;
use Laravel\Ai\Audio;
use Laravel\Ai\Transcription;

class VoiceAgent
{
    public function __construct(
        protected GemBillingService $billing,
        protected ModelCatalogService $catalog,
        protected ProviderResolver $providers,
    ) {}

    public function speak(User $user, string $text, string $provider, string $model, string $voice = 'default'): array
    {
        $meta = collect($this->catalog->forFeature('tts')[$provider] ?? [])->firstWhere('id', $model);
        abort_unless($meta, 422, 'Unknown TTS model.');

        $cost = (int) ceil(strlen($text) / 1000 * $meta['gems_per_1k_chars']);
        abort_unless($this->billing->canAfford($user, $cost), 402, 'Not enough gems for this narration.');

        $resolvedProvider = $this->providers->resolve($user, $provider);

        $audio = Audio::of($text)->voice($voice)->generate(provider: $resolvedProvider, model: $model);

        $this->billing->debit($user, $cost, reason: "tts:{$provider}/{$model}");

        return ['base64' => (string) $audio, 'gems_charged' => $cost];
    }

    public function transcribe(User $user, UploadedFile $audioFile, string $provider, string $model, bool $diarize = false): array
    {
        $meta = collect($this->catalog->forFeature('stt')[$provider] ?? [])->firstWhere('id', $model);
        abort_unless($meta, 422, 'Unknown STT model.');

        // Real duration should be pulled from the browser (it already knows
        // it) and passed alongside the upload rather than probed server-side.
        $estimatedMinutes = max(1, (int) ceil($audioFile->getSize() / (1024 * 1024 * 1))); // coarse fallback
        $cost = $estimatedMinutes * $meta['gems_per_minute'];
        abort_unless($this->billing->canAfford($user, $cost), 402, 'Not enough gems for this transcription.');

        $resolvedProvider = $this->providers->resolve($user, $provider);

        $transcript = Transcription::fromUpload($audioFile);
        if ($diarize) {
            $transcript = $transcript->diarize();
        }
        $result = $transcript->generate(provider: $resolvedProvider, model: $model);

        $this->billing->debit($user, $cost, reason: "stt:{$provider}/{$model}");

        return ['text' => (string) $result, 'gems_charged' => $cost];
    }
}
