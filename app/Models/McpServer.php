<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Laravel\Mcp\Client;

class McpServer extends Model
{
    protected $fillable = [
        'user_id', 'name', 'transport', 'url', 'command', 'args', 'auth_type',
        'token_encrypted', 'is_active',
    ];

    protected $hidden = ['token_encrypted'];

    protected function casts(): array
    {
        return ['args' => 'array', 'is_active' => 'boolean'];
    }

    public function setTokenAttribute(?string $value): void
    {
        $this->attributes['token_encrypted'] = $value ? Crypt::encryptString($value) : null;
    }

    public function token(): ?string
    {
        return $this->token_encrypted ? Crypt::decryptString($this->token_encrypted) : null;
    }

    /**
     * Build a connected Laravel MCP client for this server record.
     */
    public function client(): Client
    {
        $client = $this->transport === 'local'
            ? Client::local($this->command, $this->args ?? [])
            : Client::web($this->url);

        if ($this->auth_type === 'bearer' && $this->token()) {
            $client = $client->withToken($this->token());
        }

        return $client;
    }
}
