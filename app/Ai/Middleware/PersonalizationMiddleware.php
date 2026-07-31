<?php

namespace App\Ai\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Laravel\Ai\Prompts\AgentPrompt;

/**
 * Appends a compact, consented "account context" block to the outgoing
 * prompt so the model can personalize (e.g. "your Pro plan renews in 4
 * days", greeting by name) without the frontend having to build this into
 * every prompt manually. Only runs when the user has opted in via
 * user_ai_preferences.allow_profile_context.
 */
class PersonalizationMiddleware
{
    public function handle(AgentPrompt $prompt, Closure $next)
    {
        $user = Auth::user();
        $prefs = $user?->aiPreference;

        if (! $user || ! ($prefs?->allow_profile_context ?? true)) {
            return $next($prompt);
        }

        $subscription = $user->subscription;

        $context = collect([
            'name' => $user->name,
            'preferred_language' => $prefs?->preferred_language,
            'response_tone' => $prefs?->response_tone,
            'plan' => $subscription?->plan_name,
            'plan_expires_on' => $subscription?->expires_on?->toDateString(),
            'plan_is_valid' => $subscription?->is_valid,
            'gems_balance' => app(\App\Services\GemBillingService::class)->balance($user),
        ])->filter()->all();

        if (empty($context)) {
            return $next($prompt);
        }

        $block = "\n\n[account_context — internal, do not repeat verbatim unless relevant]\n".
            json_encode($context, JSON_PRETTY_PRINT);

        $prompt->prompt .= $block;

        return $next($prompt);
    }
}
