<?php

namespace App\Livewire\Chat;

use App\Services\ModelCatalogService;
use Livewire\Component;

class ModelPicker extends Component
{
    public string $provider = 'anthropic';

    public string $model = 'claude-sonnet-5';

    public string $thinkingMode = 'balanced';

    public bool $open = false;

    public function choose(string $provider, string $model): void
    {
        $this->provider = $provider;
        $this->model = $model;
        $this->open = false;

        $this->dispatch('model-changed', provider: $provider, model: $model);
    }

    public function chooseThinkingMode(string $mode): void
    {
        $this->thinkingMode = $mode;
        $this->dispatch('thinking-mode-changed', mode: $mode);
    }

    public function render(ModelCatalogService $catalog)
    {
        return view('livewire.chat.model-picker', [
            'models' => $catalog->availableTextModels(auth()->user())->groupBy('provider'),
            'thinkingModes' => $catalog->thinkingModes(),
        ]);
    }
}
