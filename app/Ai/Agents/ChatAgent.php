<?php

namespace App\Ai\Agents;

/**
 * ═══════════════════════════════════════════════════════════════
 * ChatAgent  –  Main conversational AI agent
 * ═══════════════════════════════════════════════════════════════
 *
 * Architecture
 * ─────────────
 * Implements three Laravel AI SDK interfaces:
 *
 *   Agent          – baseline; grants access to Promptable trait
 *   Conversational – lets us inject prior messages as context via
 *                    messages() — history comes from our custom
 *                    chat_messages table via ChatApp::withMessages()
 *   HasTools       – exposes tools the LLM can call mid-response
 *
 * We do NOT use RemembersConversations because our custom
 * chat_messages table also stores attachments, tool_calls, and
 * token counts that the SDK table does not have columns for.
 *
 * ── PHP Attributes (compile-time defaults, all overridable) ──
 *   #[Provider]     – default provider (passed at call-site too)
 *   #[Model]        – default model string
 *   #[MaxSteps]     – max tool-call round-trips per prompt
 *   #[MaxTokens]    – completion token budget
 *   #[Temperature]  – sampling temperature
 *   #[Timeout]      – HTTP timeout in seconds
 *
 * ── Fluent builder API ────────────────────────────────────────
 *   ->withMessages(array)     inject conversation history
 *   ->withContext(string)     append extra system context
 *   ->withWebSearch(bool)     enable/disable WebSearch tool
 *   ->withWebFetch(bool)      enable/disable WebFetch tool
 *
 * ── Provider / tool compatibility ────────────────────────────
 *   WebSearch + WebFetch are provider-side tools: they run inside
 *   Anthropic / OpenAI / Gemini's own infrastructure.
 *   They are NOT supported by Ollama or DeepSeek.
 *
 *   ChatApp::doStream() detects the selected provider and calls
 *   ->withWebSearch(false)->withWebFetch(false) for local/
 *   unsupported providers before instantiating the agent.
 *
 *   CalculatorTool and DateTimeTool run server-side in Laravel
 *   and work with any provider that supports tool calling.
 * ═══════════════════════════════════════════════════════════════
 */

use App\Ai\Tools\CalculatorTool;
use App\Ai\Tools\DateTimeTool;
use Laravel\Ai\Attributes\MaxSteps;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Messages\Message as AiMessage;
use Laravel\Ai\Promptable;
use Laravel\Ai\Providers\Tools\WebFetch;
use Laravel\Ai\Providers\Tools\WebSearch;
use Stringable;

// ── Default provider / model ──────────────────────────────────
// These are compile-time defaults only.
// The actual provider + model are passed at call-site:
//   $agent->stream($prompt, provider: 'openai', model: 'gpt-4o')
//
// For LOCAL DEVELOPMENT with Ollama, comment-swap these:
//#[Provider(Lab::Anthropic)]
//#[Model('claude-sonnet-4-6')]
 #[Provider(Lab::Ollama)]
 #[Model('qwen3.5:0.8b')]
#[MaxSteps(25)]
#[MaxTokens(8192)]
#[Temperature(0.7)]
#[Timeout(180)]
class ChatAgent implements Agent, Conversational, HasTools
{
    use Promptable;

    // ──────────────────────────────────────────────────────────
    // Internal state — set via fluent builder methods below
    // ──────────────────────────────────────────────────────────

    /** @var AiMessage[]  Prior conversation turns. */
    private array $conversationMessages = [];

    /** Extra text appended to the end of the system instructions. */
    private string $additionalContext = '';

    /**
     * Whether the WebSearch provider tool should be included.
     * Set to false automatically for Ollama/DeepSeek in ChatApp.
     */
    private bool $webSearchEnabled = true;

    /**
     * Whether the WebFetch provider tool should be included.
     * Set to false automatically for Ollama/DeepSeek in ChatApp.
     */
    private bool $webFetchEnabled = true;

    // ──────────────────────────────────────────────────────────
    // Fluent builder API
    // ──────────────────────────────────────────────────────────

    /**
     * Inject the full conversation history.
     * Called by ChatApp::doStream() before each stream() call.
     *
     * @param  AiMessage[]  $messages
     */
    public function withMessages(array $messages): static
    {
        $this->conversationMessages = $messages;
        return $this;
    }

    /**
     * Append extra text to the system instructions.
     * Useful for per-user preferences or persona overrides.
     */
    public function withContext(string $context): static
    {
        $this->additionalContext = $context;
        return $this;
    }

    /**
     * Enable or disable the WebSearch provider tool.
     * ChatApp passes false for providers that don't support it.
     */
    public function withWebSearch(bool $enabled): static
    {
        $this->webSearchEnabled = $enabled;
        return $this;
    }

    /**
     * Enable or disable the WebFetch provider tool.
     * ChatApp passes false for providers that don't support it.
     */
    public function withWebFetch(bool $enabled): static
    {
        $this->webFetchEnabled = $enabled;
        return $this;
    }

    // ──────────────────────────────────────────────────────────
    // Agent interface: system instructions
    // ──────────────────────────────────────────────────────────

    /**
     * System-level instructions prepended to every request.
     *
     * Keep descriptions factual and capability-specific.
     * The model responds better to concrete guidance than
     * theatrical "you are a friendly assistant" framing.
     */
    public function instructions(): Stringable|string
    {
        $date = now()->format('l, F j, Y \a\t g:i A T');

        $base = <<<SYSTEM
You are a highly capable AI assistant built into a modern chat interface.

## Capabilities
- Answer questions on any topic with accuracy and nuance
- Search the web for real-time information and current events
- Fetch and read content from specific URLs provided by the user
- Perform precise mathematical calculations
- Provide date/time information across all timezones
- Analyse images, PDFs, and documents attached by the user
- Write, explain, debug, and review code in any language
- Help with writing, brainstorming, research, and analysis

## Response Formatting
- Use Markdown for all structured responses (headings, lists, code blocks)
- Always add a language identifier to fenced code blocks (```php, ```python, etc.)
- Match explanation depth to question complexity — thorough but not padded
- When uncertain, say so clearly and provide your best reasoning
- Cite sources naturally when using web search results

## Tool Usage
- Use WebSearch for current events, prices, news, or anything time-sensitive
- Use WebFetch when the user gives you a specific URL to read
- Use the Calculator for any numeric computation — never compute mentally
- Use the DateTime tool for date/time questions or timezone conversions

## Current Date & Time
{$date}
SYSTEM;

        if ($this->additionalContext) {
            $base .= "\n\n## Additional Context\n{$this->additionalContext}";
        }

        return $base;
    }

    // ──────────────────────────────────────────────────────────
    // Agent interface: conversation history
    //
    // ⚠️  Do NOT rename this to something else or add
    //     RemembersConversations — that trait provides its OWN
    //     messages() implementation. If both exist, the trait's
    //     version wins and our history injection is silently lost.
    // ──────────────────────────────────────────────────────────

    /**
     * Returns the conversation history injected by withMessages().
     * ChatApp builds this array from chat_messages rows before
     * calling stream()/prompt().
     *
     * @return AiMessage[]
     */
    public function messages(): iterable
    {
        return $this->conversationMessages;
    }

    // ──────────────────────────────────────────────────────────
    // Agent interface: tools
    // ──────────────────────────────────────────────────────────

    /**
     * Tools available to the LLM each turn.
     *
     * Server-side tools (run in THIS Laravel process):
     *   CalculatorTool  – safe eval of math expressions
     *   DateTimeTool    – date/time operations with timezone support
     *   → Both work with any provider that supports tool calling.
     *
     * Provider-side tools (run inside the AI provider's infra):
     *   WebSearch  – real-time web search (Anthropic / OpenAI / Gemini)
     *   WebFetch   – read a URL's content (Anthropic / Gemini)
     *   → Only added when $webSearchEnabled / $webFetchEnabled are true.
     *   → ChatApp sets both to false for Ollama, DeepSeek, and other
     *     local providers that do not support provider-side tools.
     */
    public function tools(): iterable
    {
        $tools = [
            // ── Server-side tools — work with any provider ────
            new CalculatorTool,
            new DateTimeTool,
        ];

        // ── Provider-side tools — Anthropic / OpenAI / Gemini only ──
        // These flags are set by ChatApp based on $this->selectedProvider
        // before calling stream(). For Ollama / DeepSeek they are false.
        if ($this->webSearchEnabled) {
            $tools[] = (new WebSearch)->max(10); // up to 10 searches per response
        }

        if ($this->webFetchEnabled) {
            $tools[] = (new WebFetch)->max(5);   // up to 5 URL fetches per response
        }

        return $tools;
    }
}
