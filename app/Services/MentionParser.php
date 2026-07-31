<?php

namespace App\Services;

use App\Models\Skill;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Parses `@tool_slug` and `#skill_slug` mentions out of the raw composer
 * text (the same standard used by Slack/Notion-style pickers, so it matches
 * what the dropdown inserts when a user selects an item instead of typing
 * it). Mentions are stripped from the text sent to the model's "user turn"
 * and instead injected as structured tool/skill activations.
 */
class MentionParser
{
    public function parse(string $prompt, User $user): array
    {
        preg_match_all('/@([a-z0-9\-_]+)/i', $prompt, $toolMatches);
        preg_match_all('/#([a-z0-9\-_]+)/i', $prompt, $skillMatches);

        $tools = Tool::query()->availableTo($user)
            ->whereIn('slug', $toolMatches[1] ?? [])
            ->get();

        $skills = Skill::query()->availableTo($user)
            ->whereIn('slug', $skillMatches[1] ?? [])
            ->get();

        $cleanPrompt = trim(preg_replace(['/@[a-z0-9\-_]+/i', '/#[a-z0-9\-_]+/i'], '', $prompt));

        return [
            'prompt' => $cleanPrompt !== '' ? $cleanPrompt : $prompt,
            'tools' => $tools,
            'skills' => $skills,
        ];
    }

    /**
     * Merge a project's default tools/skills with anything explicitly
     * @/# mentioned in this specific message.
     */
    public function mergeProjectDefaults(Collection $tools, Collection $skills, ?array $projectToolIds, ?array $projectSkillIds): array
    {
        if ($projectToolIds) {
            $tools = $tools->merge(Tool::whereIn('id', $projectToolIds)->get())->unique('id');
        }
        if ($projectSkillIds) {
            $skills = $skills->merge(Skill::whereIn('id', $projectSkillIds)->get())->unique('id');
        }

        return [$tools, $skills];
    }
}
