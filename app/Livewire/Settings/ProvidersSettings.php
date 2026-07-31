<?php

namespace App\Livewire\Settings;

use App\Models\UserProviderCredential;
use Livewire\Component;

class ProvidersSettings extends Component
{
    public string $provider = 'openai';

    public ?string $label = null;

    public string $apiKey = '';

    public ?string $baseUrl = null;

    public bool $showForm = false;

    public function save(): void
    {
        $this->validate([
            'provider' => 'required|string',
            'apiKey' => 'required_unless:provider,ollama|string|nullable',
            'baseUrl' => 'nullable|url',
            'label' => 'nullable|string|max:60',
        ]);

        $cred = auth()->user()->providerCredentials()->updateOrCreate(
            ['provider' => $this->provider, 'label' => $this->label],
            ['base_url' => $this->baseUrl, 'is_active' => true],
        );

        if ($this->apiKey) {
            $cred->setApiKeyAttribute($this->apiKey);
            $cred->save();
        }

        $this->reset(['apiKey', 'baseUrl', 'label', 'showForm']);
    }

    public function remove(UserProviderCredential $credential): void
    {
        abort_unless($credential->user_id === auth()->id(), 403);
        $credential->delete();
    }

    public function render()
    {
        return view('livewire.settings.providers-settings', [
            'credentials' => auth()->user()->providerCredentials()->get(),
            'providerOptions' => config('ai_models.providers'),
        ]);
    }
}
