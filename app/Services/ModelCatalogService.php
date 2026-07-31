<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Single point of truth the ModelPicker Livewire component, ChatAgent, and
 * billing pre-flight all use to know which provider/model combos exist and
 * which ones a given user is actually allowed to call (plan limits, BYO key
 * presence for keys the platform doesn't pool, local-only Ollama gating).
 */
class ModelCatalogService
{
    public function forFeature(string $feature): array
    {
        return config("ai_models.{$feature}", []);
    }

    /**
     * Flatten config into UI-friendly [provider, model] rows, filtered to
     * what the user may actually use right now.
     */
    public function availableTextModels(User $user): Collection
    {
        $credentialedProviders = $user->providerCredentials()
            ->where('is_active', true)
            ->pluck('provider')
            ->push('platform-managed') // sentinel — platform-managed keys handled separately
            ->all();

        $managedProviders = collect(config('ai_models.providers'))
            ->filter(fn ($p) => $p['managed'] ?? false)
            ->keys();

        return collect($this->forFeature('text'))
            ->flatMap(function ($models, $provider) use ($credentialedProviders, $managedProviders) {
                $usable = $managedProviders->contains($provider) || in_array($provider, $credentialedProviders) || $provider === 'ollama';

                return collect($models)->map(fn ($m) => [
                    'provider' => $provider,
                    'provider_label' => config("ai_models.providers.{$provider}.label", $provider),
                    'usable' => $usable,
                    ...$m,
                ]);
            })
            ->values();
    }

    public function findTextModel(string $provider, string $modelId): ?array
    {
        return collect($this->forFeature('text')[$provider] ?? [])
            ->firstWhere('id', $modelId);
    }

    public function thinkingModes(): array
    {
        return config('ai_models.thinking_modes');
    }
}
