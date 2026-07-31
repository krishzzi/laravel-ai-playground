<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatArtifact extends Model
{
    use HasUuids;

    protected $fillable = [
        'chat_message_id', 'type', 'filename', 'mime', 'language', 'content', 'disk_path', 'size_bytes',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'chat_message_id');
    }

    /**
     * Artifacts are, by default, never persisted server-side — the browser
     * builds the zip/file client-side (JSZip / File System Access API) from
     * `content` and streams it straight to the user's disk. `disk_path` is
     * only populated when a file legitimately must live on the server (e.g.
     * a generated image the user chose to keep in their library).
     */
    public function isBrowserOnly(): bool
    {
        return blank($this->disk_path);
    }
}
