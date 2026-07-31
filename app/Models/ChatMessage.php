<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Ai\Messages\Message as SdkMessage;

class ChatMessage extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'chat_session_id', 'role', 'content', 'attachments', 'tool_calls', 'citations',
        'provider', 'model', 'thinking_mode', 'thinking_trace', 'input_tokens', 'output_tokens',
        'gems_charged', 'edited', 'branched_from_message_id',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'tool_calls' => 'array',
            'citations' => 'array',
            'edited' => 'boolean',
        ];
    }

    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }

    public function artifacts(): HasMany
    {
        return $this->hasMany(ChatArtifact::class);
    }

    public function toSdkMessage(): SdkMessage
    {
        return new SdkMessage($this->role, (string) $this->content);
    }
}
