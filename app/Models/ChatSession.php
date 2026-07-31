<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSession extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id', 'project_id', 'title', 'mode', 'default_provider', 'default_model',
        'thinking_mode', 'pinned', 'archived', 'meta', 'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'pinned' => 'boolean',
            'archived' => 'boolean',
            'last_activity_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at');
    }

    public function touchActivity(): void
    {
        $this->forceFill(['last_activity_at' => now()])->save();
    }

    /**
     * Auto-title a new session from the first user message (cheap model, no
     * gem charge — this is done with a truncation heuristic first and only
     * falls back to a model call from the queued job if needed).
     */
    public function autoTitleFrom(string $firstMessage): string
    {
        return \Illuminate\Support\Str::limit(trim($firstMessage), 60);
    }
}
