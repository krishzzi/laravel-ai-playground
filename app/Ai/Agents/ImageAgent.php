<?php

namespace App\Ai\Agents;

use App\Models\User;
use App\Services\GemBillingService;
use App\Services\ModelCatalogService;
use App\Services\ProviderResolver;
use Illuminate\Http\UploadedFile;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Files;
use Laravel\Ai\Image;

/**
 * Thin application-layer wrapper around Laravel\Ai\Image that adds: gem
 * pre-flight + debit, per-user provider credential resolution, and
 * image-to-image via reference attachments (still processed client-side
 * first when possible — see resources/js/file-ingest.js — this only runs
 * when the provider truly needs the raw bytes, e.g. actual pixel edits).
 */
class ImageAgent
{
    public function __construct(
        protected GemBillingService $billing,
        protected ModelCatalogService $catalog,
        protected ProviderResolver $providers,
    ) {}

    /**
     * @param  UploadedFile[]  $referenceImages
     */
    public function generate(User $user, string $prompt, string $provider, string $model, array $referenceImages = [], string $aspect = 'square'): array
    {
        $meta = collect($this->catalog->forFeature('image')[$provider] ?? [])->firstWhere('id', $model);
        abort_unless($meta, 422, 'Unknown image model.');

        $cost = $meta['gems_each'];
        abort_unless($this->billing->canAfford($user, $cost), 402, 'Not enough gems for this image.');

        $resolvedProvider = $this->providers->resolve($user, $provider);

        $image = Image::of($prompt);

        $image = match ($aspect) {
            'portrait' => $image->portrait(),
            'landscape' => $image->landscape(),
            default => $image->square(),
        };

        if (! empty($referenceImages)) {
            $image = $image->attachments(array_map(
                fn (UploadedFile $file) => $file,
                $referenceImages,
            ));
        }

        $result = $image->generate(provider: $resolvedProvider, model: $model);

        $this->billing->debit($user, $cost, reason: "image:{$provider}/{$model}");

        return [
            'base64' => (string) $result,
            'gems_charged' => $cost,
        ];
    }
}
