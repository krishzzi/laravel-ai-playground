<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Model
{
    protected $fillable = [
        'user_id', 'slug', 'name', 'description', 'instructions', 'linked_tool_ids',
        'is_public', 'is_active',
    ];

    protected function casts(): array
    {
        return ['linked_tool_ids' => 'array', 'is_public' => 'boolean', 'is_active' => 'boolean'];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeAvailableTo($query, User $user)
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->where('is_public', true)->orWhere('user_id', $user->id));
    }
}
