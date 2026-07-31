<?php

namespace App\Livewire\Studio;

use App\Ai\Agents\ImageAgent;
use App\Exceptions\InsufficientGemsException;
use App\Services\ModelCatalogService;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImageStudio extends Component
{
    use WithFileUploads;

    #[Validate('required|string|max:4000')]
    public string $prompt = '';

    public string $provider = 'openai';

    public string $model = 'gpt-image-1';

    public string $aspect = 'square';

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile[] */
    public array $references = [];

    public array $gallery = []; // [{base64, prompt, gems_charged}]

    public bool $generating = false;

    public function generate(ImageAgent $agent): void
    {
        $this->validate();
        $this->generating = true;

        try {
            $result = $agent->generate(
                auth()->user(),
                $this->prompt,
                $this->provider,
                $this->model,
                $this->references,
                $this->aspect,
            );

            $this->gallery[] = [
                'base64' => $result['base64'],
                'prompt' => $this->prompt,
                'gems_charged' => $result['gems_charged'],
            ];
        } catch (InsufficientGemsException $e) {
            $this->addError('gems', $e->getMessage());
        } finally {
            $this->generating = false;
        }
    }

    public function useAsReference(int $index): void
    {
        // Feeds a previously generated image back in as an I2I reference —
        // the browser turns the stored base64 into a Blob/File client-side
        // and re-submits it via the file input, keeping this consistent
        // with the "never re-upload what we already have" principle.
        $this->dispatch('reuse-as-reference', base64: $this->gallery[$index]['base64']);
    }

    public function render(ModelCatalogService $catalog)
    {
        return view('livewire.studio.image-studio', [
            'models' => $catalog->forFeature('image'),
        ]);
    }
}
