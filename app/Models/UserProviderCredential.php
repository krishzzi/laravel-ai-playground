<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class UserProviderCredential extends Model
{
    protected $fillable = ['user_id', 'provider', 'label', 'base_url', 'is_active'];

    protected $hidden = ['api_key_encrypted'];

    public function setApiKeyAttribute(?string $value): void
    {
        $this->attributes['api_key_encrypted'] = $value ? Crypt::encryptString($value) : null;
    }

    public function apiKey(): ?string
    {
        return $this->api_key_encrypted ? Crypt::decryptString($this->api_key_encrypted) : null;
    }
}
