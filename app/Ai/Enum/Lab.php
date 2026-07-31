<?php

use Laravel\Ai\Enums\Lab;

/**
 * Central catalog of every model this platform is allowed to expose to users,
 * grouped by capability. This is the single source of truth the ModelPicker,
 * GemBillingService (cost weighting) and ChatAgent (validation) all read from.
 *
 * Edit this file (or move it to the database via `ai_models` table + an admin
 * screen) to add/remove models without touching application code.
 */
return [

    // Providers a user is allowed to bring their own key for, or that the
    // platform provides via pooled/managed keys.
    'providers' => [
        Lab::OpenAI->value => ['label' => 'OpenAI', 'byo_key' => true, 'managed' => true],
        Lab::OpenAiCompatible->value => ['label' => 'OpenAI Compatible', 'byo_key' => true, 'managed' => false],
        Lab::Anthropic->value => ['label' => 'Anthropic', 'byo_key' => true, 'managed' => true],
        Lab::Gemini->value => ['label' => 'Google Gemini', 'byo_key' => true, 'managed' => true],
        'openrouter' => ['label' => 'OpenRouter', 'byo_key' => true, 'managed' => true],
        Lab::Groq->value => ['label' => 'Groq', 'byo_key' => true, 'managed' => true],
        'elevenlabs' => ['label' => 'ElevenLabs', 'byo_key' => true, 'managed' => true],
        'ollama' => ['label' => 'Ollama (local only)', 'byo_key' => false, 'managed' => false, 'local_only' => true],
        // Any self-hosted inference endpoint (RunPod, AWS GPU box, vLLM, etc.)
        'custom' => ['label' => 'Custom Server', 'byo_key' => true, 'managed' => false, 'user_defined' => true],
    ],

    // Text / chat models. `gems_per_1k_in` / `gems_per_1k_out` drive the
    // GemBillingService cost calc — tune these against your real provider
    // invoice + margin target.
    'text' => [
        Lab::Anthropic->value => [
            ['id' => 'claude-sonnet-5', 'label' => 'Claude Sonnet 5', 'context' => 200000, 'thinking' => true, 'gems_in' => 3, 'gems_out' => 15],
            ['id' => 'claude-opus-4-8', 'label' => 'Claude Opus 4.8', 'context' => 200000, 'thinking' => true, 'gems_in' => 15, 'gems_out' => 75],
            ['id' => 'claude-haiku-4-5-20251001', 'label' => 'Claude Haiku 4.5', 'context' => 200000, 'thinking' => false, 'gems_in' => 1, 'gems_out' => 5],
        ],
        Lab::OpenAI->value => [
            ['id' => 'gpt-5.1', 'label' => 'GPT-5.1', 'context' => 400000, 'thinking' => true, 'gems_in' => 3, 'gems_out' => 12],
            ['id' => 'gpt-5.1-mini', 'label' => 'GPT-5.1 Mini', 'context' => 400000, 'thinking' => false, 'gems_in' => 1, 'gems_out' => 4],
        ],
        Lab::Gemini->value => [
            ['id' => 'gemini-3-pro', 'label' => 'Gemini 3 Pro', 'context' => 1000000, 'thinking' => true, 'gems_in' => 2, 'gems_out' => 10],
            ['id' => 'gemini-3-flash', 'label' => 'Gemini 3 Flash', 'context' => 1000000, 'thinking' => false, 'gems_in' => 1, 'gems_out' => 3],
        ],
        Lab::Groq->value => [
            ['id' => 'llama-3.3-70b-versatile', 'label' => 'Llama 3.3 70B (Groq)', 'context' => 128000, 'thinking' => false, 'gems_in' => 1, 'gems_out' => 2],
        ],
        'openrouter' => [
            ['id' => 'deepseek/deepseek-v4', 'label' => 'DeepSeek V4 (OpenRouter)', 'context' => 128000, 'thinking' => true, 'gems_in' => 1, 'gems_out' => 3],
        ],
        'ollama' => [
            ['id' => 'llama3.1', 'label' => 'Llama 3.1 (local)', 'context' => 32000, 'thinking' => false, 'gems_in' => 0, 'gems_out' => 0],
        ],
    ],

    // Image generation
    'image' => [
        Lab::OpenAI->value => [
            ['id' => 'gpt-image-1', 'label' => 'GPT Image 1', 'gems_each' => 20],
        ],
        Lab::Gemini->value => [
            ['id' => 'gemini-3-flash-image', 'label' => 'Nano Banana (Gemini Image)', 'gems_each' => 15],
        ],
        'openrouter' => [
            ['id' => 'black-forest-labs/flux-1.1-pro', 'label' => 'FLUX 1.1 Pro', 'gems_each' => 25],
        ],
    ],

    // Text to speech
    'tts' => [
        Lab::OpenAI->value => [['id' => 'gpt-4o-mini-tts', 'label' => 'OpenAI TTS', 'gems_per_1k_chars' => 5]],
        'elevenlabs' => [['id' => 'eleven_v3', 'label' => 'ElevenLabs v3', 'gems_per_1k_chars' => 10]],
        Lab::Gemini->value => [['id' => 'gemini-2.5-flash-tts', 'label' => 'Gemini TTS', 'gems_per_1k_chars' => 4]],
    ],

    // Speech to text
    'stt' => [
        Lab::OpenAI->value => [['id' => 'whisper-1', 'label' => 'Whisper', 'gems_per_minute' => 5]],
        'elevenlabs' => [['id' => 'scribe-v1', 'label' => 'Scribe', 'gems_per_minute' => 5]],
        Lab::Gemini->value => [['id' => 'gemini-2.5-flash', 'label' => 'Gemini STT', 'gems_per_minute' => 4]],
    ],

    // Embeddings (used by the browser-side file ingestion pipeline)
    'embeddings' => [
        Lab::OpenAI->value => [['id' => 'text-embedding-3-small', 'label' => 'text-embedding-3-small', 'dimensions' => 1536, 'gems_per_1k' => 1]],
        Lab::Gemini->value => [['id' => 'gemini-embedding-001', 'label' => 'Gemini Embedding', 'dimensions' => 1536, 'gems_per_1k' => 1]],
    ],

    // Reranking — Jina picked as default: cheap, fast, no min spend.
    'rerank' => [
        'default' => 'jina',
        'jina' => [['id' => 'jina-reranker-v3', 'label' => 'Jina Reranker v3']],
    ],

    // Thinking modes exposed in the composer's "brain" selector. Each maps to
    // provider options merged in via the agent's providerOptions() method.
    'thinking_modes' => [
        'instant' => ['label' => 'Instant', 'budget_tokens' => 0, 'gems_multiplier' => 1.0],
        'balanced' => ['label' => 'Balanced Thinking', 'budget_tokens' => 4096, 'gems_multiplier' => 1.3],
        'deep' => ['label' => 'Deep Research Thinking', 'budget_tokens' => 16384, 'gems_multiplier' => 2.0],
        'max' => ['label' => 'Max Reasoning', 'budget_tokens' => 32768, 'gems_multiplier' => 3.0],
    ],
];
