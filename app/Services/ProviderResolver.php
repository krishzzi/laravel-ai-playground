<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Config;

/**
 * The Laravel AI SDK reads provider credentials from config/ai.php at
 * request-boot time. For BYO-key providers and user-defined custom servers
 * (RunPod / self-hosted vLLM / etc.) we override that config per-request,
 * scoped to the authenticated user, before the agent is prompted.
 */
class ProviderResolver
{
    /**
     * Apply a user's stored credential (if any) for $provider into the
     * runtime `ai.providers.<provider>` config, and return the provider key
     * the AI SDK should be called with.
     */
    public function resolve(User $user, string $provider, ?string $label = null): string
    {
        // Custom/self-hosted servers are always modeled as an
        // "openai-compatible" driver instance, one per saved server.
        if ($provider === 'custom') {
            $cred = $user->providerCredentials()
                ->where('provider', 'custom')
                ->where('label', $label)
                ->where('is_active', true)
                ->firstOrFail();

            $key = 'custom_'.\Illuminate\Support\Str::slug($label ?? 'server');

            Config::set("ai.providers.{$key}", [
                'driver' => 'openai-compatible',
                'url' => $cred->base_url,
                'key' => $cred->apiKey(),
            ]);

            return $key;
        }

        $isManaged = config("ai_models.providers.{$provider}.managed", false);

        $cred = $user->providerCredentials()
            ->where('provider', $provider)
            ->where('is_active', true)
            ->first();

        // Managed providers use the platform's pooled key unless the user
        // explicitly supplied their own (which they may prefer, e.g. to use
        // their own OpenAI billing / higher rate limits).
        if ($cred) {
            Config::set("ai.providers.{$provider}.key", $cred->apiKey());
            if ($cred->base_url) {
                Config::set("ai.providers.{$provider}.url", $cred->base_url);
            }
        } elseif (! $isManaged && $provider !== 'ollama') {
            throw new \RuntimeException("No API credentials configured for provider [{$provider}]. Add one in Settings.");
        }

        return $provider;
    }
}
