<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'color', 'instructions', 'default_tool_ids', 'default_skill_ids',
    ];

    protected function casts(): array
    {
        return [
            'default_tool_ids' => 'array',
            'default_skill_ids' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class);
    }
}
