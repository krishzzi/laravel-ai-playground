<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAiPreference extends Model
{
    protected $fillable = [
        'user_id', 'preferred_language', 'response_tone', 'default_thinking_mode',
        'allow_profile_context', 'custom_instructions',
    ];

    protected function casts(): array
    {
        return ['allow_profile_context' => 'boolean', 'custom_instructions' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
