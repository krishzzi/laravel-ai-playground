<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tool extends Model
{
    protected $fillable = [
        'user_id', 'slug', 'name', 'icon', 'description', 'prompt_template', 'kind',
        'mcp_server_id', 'provider_tool_class', 'config', 'is_public', 'is_active',
    ];

    protected function casts(): array
    {
        return ['config' => 'array', 'is_public' => 'boolean', 'is_active' => 'boolean'];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mcpServer(): BelongsTo
    {
        return $this->belongsTo(McpServer::class);
    }

    public function scopeAvailableTo($query, User $user)
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->where('is_public', true)->orWhere('user_id', $user->id));
    }
}
