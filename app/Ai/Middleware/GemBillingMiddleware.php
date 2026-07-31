<?php

namespace App\Ai\Middleware;

use App\Models\ChatSession;
use App\Services\GemBillingService;
use App\Services\ModelCatalogService;
use Closure;
use Laravel\Ai\Prompts\AgentPrompt;
use Laravel\Ai\Responses\AgentResponse;

/**
 * Blocks a prompt before it reaches the provider if the user cannot afford a
 * conservative estimate, then reconciles the real charge against actual
 * token usage once the response comes back (thinking tokens included).
 */
class GemBillingMiddleware
{
    public function __construct(protected ChatSession $session) {}

    public function handle(AgentPrompt $prompt, Closure $next)
    {
        $billing = app(GemBillingService::class);
        $catalog = app(ModelCatalogService::class);
        $user = $this->session->user;

        $modelMeta = $catalog->findTextModel(
            $this->session->default_provider ?? 'anthropic',
            $this->session->default_model ?? 'claude-sonnet-5',
        ) ?? ['gems_in' => 2, 'gems_out' => 8];

        // Rough pre-flight estimate: ~4 chars/token, assume output roughly
        // matches a generous ceiling so we never let a user go negative.
        $estimatedInput = (int) (strlen($prompt->prompt) / 4) + 512; // + running context overhead
        $estimatedOutput = 2048;

        $estimate = $billing->estimateTextCost($modelMeta, $estimatedInput, $estimatedOutput, $this->session->thinking_mode);

        if (! $billing->canAfford($user, $estimate)) {
            throw new \App\Exceptions\InsufficientGemsException($estimate, $billing->balance($user));
        }

        /** @var AgentResponse $response */
        $response = $next($prompt);

        return $response->then(function (AgentResponse $response) use ($billing, $catalog, $user, $modelMeta) {
            $in = $response->usage->inputTokens ?? 0;
            $out = $response->usage->outputTokens ?? 0;

            $actual = $billing->estimateTextCost($modelMeta, $in, $out, $this->session->thinking_mode);

            $billing->debit(
                $user,
                max(1, $actual),
                reason: "text:{$this->session->default_provider}/{$this->session->default_model}",
                meta: ['input_tokens' => $in, 'output_tokens' => $out, 'thinking_mode' => $this->session->thinking_mode],
            );

            return $response;
        });
    }
}
