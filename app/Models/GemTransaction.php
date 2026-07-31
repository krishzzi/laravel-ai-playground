<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GemTransaction extends Model
{
    protected $fillable = [
        'user_id', 'chat_message_id', 'type', 'amount', 'balance_after', 'reason', 'meta',
    ];

    protected function casts(): array
    {
        return ['meta' => 'array'];
    }
}
