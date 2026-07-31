<?php

namespace App\Ai\Tools;

use App\Models\Tool as PlatformTool;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Adapts a user- or platform-authored `tools` DB record into a callable AI
 * SDK tool. For simple "prompt" kind tools this just returns the stored
 * template filled with the model's arguments — enough for things like
 * "@summarize_url", "@sql_explainer", "@regex_builder" without writing a
 * bespoke PHP class per tool.
 */
class SkillTool implements Tool
{
    public function __construct(protected PlatformTool $record) {}

    public function description(): Stringable|string
    {
        return $this->record->description;
    }

    public function handle(Request $request): Stringable|string
    {
        $template = $this->record->prompt_template ?? '{{input}}';

        return str_replace('{{input}}', (string) $request['input'], $template);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'input' => $schema->string()
                ->description("The input to pass to the '{$this->record->name}' tool.")
                ->required(),
        ];
    }
}
