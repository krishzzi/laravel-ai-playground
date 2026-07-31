<?php

namespace App\Models\Concerns;

use App\Models\ChatSession;
use App\Models\GemWallet;
use App\Models\McpServer;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Tool;
use App\Models\UserAiPreference;
use App\Models\UserProviderCredential;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Add `use HasAiWorkspace;` to App\Models\User. Kept as a trait rather than
 * editing User.php directly so this scaffold doesn't clobber whatever your
 * existing User model (Jetstream/Breeze/Fortify + Cashier) already has.
 *
 * Expects the host User model to expose ->subscription (from your billing
 * package of choice) with ->plan_name, ->expires_on, ->is_valid — adapt the
 * accessor below to match Cashier/Paddle's actual API if it differs.
 */
trait HasAiWorkspace
{
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class);
    }

    public function tools(): HasMany
    {
        return $this->hasMany(Tool::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    public function mcpServers(): HasMany
    {
        return $this->hasMany(McpServer::class);
    }

    public function providerCredentials(): HasMany
    {
        return $this->hasMany(UserProviderCredential::class);
    }

    public function gemWallet(): HasOne
    {
        return $this->hasOne(GemWallet::class);
    }

    public function aiPreference(): HasOne
    {
        return $this->hasOne(UserAiPreference::class);
    }

    /**
     * Compact, PII-minimal snapshot the ChatAgent's PersonalizationMiddleware
     * is allowed to surface to the model. Deliberately excludes mobile
     * number, DOB, and sex unless a future feature explicitly needs them
     * (e.g. an age-gated tool) — keep the default footprint small.
     */
    public function aiProfileSnapshot(): array
    {
        return [
            'name' => $this->name,
            'preferred_language' => $this->aiPreference?->preferred_language ?? 'en',
            'plan' => $this->subscription?->plan_name ?? 'free',
            'plan_is_valid' => $this->subscription?->is_valid ?? false,
        ];
    }
}
