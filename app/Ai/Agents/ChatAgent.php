<?php

namespace App\Ai\Agents;

use App\Ai\Middleware\GemBillingMiddleware;
use App\Ai\Middleware\PersonalizationMiddleware;
use App\Ai\Tools\DynamicMcpToolLoader;
use App\Ai\Tools\SkillTool;
use App\Models\ChatSession;
use App\Models\Skill;
use App\Models\Tool as PlatformTool;
use Illuminate\Support\Collection;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasMiddleware;
use Laravel\Ai\Contracts\HasProviderOptions;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Laravel\Ai\Providers\Tools\WebFetch;
use Laravel\Ai\Providers\Tools\WebSearch;
use Stringable;

class ChatAgent implements Agent, Conversational, HasMiddleware, HasProviderOptions, HasTools
{
    use Promptable;

    protected Collection $activeTools;

    protected Collection $activeSkills;

    public function __construct(
        public ChatSession $session,
        Collection $tools = new Collection,
        Collection $skills = new Collection,
    ) {
        $this->activeTools = $tools;
        $this->activeSkills = $skills;
    }

    public function instructions(): Stringable|string
    {
        $base = <<<'PROMPT'
        You are the assistant inside a premium multi-model AI workspace. You have
        access to the user's account context (name, plan, preferences) when they
        have consented to share it — use it only when it genuinely improves the
        answer, never to show off that you have it. Be direct, accurate, and
        efficient with the user's token/gem budget: don't pad answers, don't
        repeat the question back, and prefer showing a runnable artifact (code,
        file, image) over describing one when the user asked for a deliverable.

        When a request implies a downloadable output (a script, a zip, a
        document), produce it as a structured artifact block so the UI can
        render it in the split-view panel and let the user download it directly
        from their browser rather than a server round-trip.
        PROMPT;

        $skillInstructions = $this->activeSkills
            ->map(fn (Skill $s) => "### Skill: {$s->name}\n{$s->instructions}")
            ->implode("\n\n");

        $projectInstructions = $this->session->project?->instructions;

        return collect([$base, $projectInstructions, $skillInstructions])
            ->filter()
            ->implode("\n\n---\n\n");
    }

    public function messages(): iterable
    {
        return $this->session->messages()
            ->whereIn('role', ['user', 'assistant'])
            ->get()
            ->map(fn ($m) => new Message($m->role, (string) $m->content))
            ->all();
    }

    /**
     * @return \Laravel\Ai\Contracts\Tool[]
     */
    public function tools(): iterable
    {
        $tools = [
            (new WebSearch)->max(6),
            new WebFetch,
        ];

        foreach ($this->activeTools as $tool) {
            $tools[] = match ($tool->kind) {
                'mcp' => null, // handled below via bulk MCP loader (needs server)
                'provider_tool' => app($tool->provider_tool_class),
                default => new SkillTool($tool), // 'prompt' kind: a lightweight instruction-injecting tool
            };
        }

        $mcpToolSlugs = $this->activeTools->where('kind', 'mcp')->pluck('mcp_server_id')->filter()->unique();
        foreach ($mcpToolSlugs as $serverId) {
            $tools = array_merge($tools, (new DynamicMcpToolLoader)->forServer($serverId));
        }

        return array_filter($tools);
    }

    public function middleware(): array
    {
        return [
            new PersonalizationMiddleware,
            new GemBillingMiddleware($this->session),
        ];
    }

    public function providerOptions(Lab|string $provider): array
    {
        $mode = config("ai_models.thinking_modes.{$this->session->thinking_mode}");
        $budget = $mode['budget_tokens'] ?? 0;

        return match (true) {
            $provider === Lab::Anthropic && $budget > 0 => [
                'thinking' => ['type' => 'enabled', 'budget_tokens' => $budget],
                'cache_control' => ['type' => 'ephemeral'],
            ],
            $provider === Lab::OpenAI && $budget > 0 => [
                'reasoning' => ['effort' => $budget >= 16384 ? 'high' : 'medium'],
            ],
            $provider === Lab::Gemini && $budget > 0 => [
                'thinking_config' => ['thinking_budget' => $budget],
            ],
            default => [],
        };
    }
}
