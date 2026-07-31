<?php

namespace App\Livewire\Chat;

use App\Models\ChatArtifact;
use Livewire\Attributes\On;
use Livewire\Component;

class SplitViewPanel extends Component
{
    public ?ChatArtifact $artifact = null;

    #[On('open-split-view')]
    public function load(?string $artifactUuid = null): void
    {
        if ($artifactUuid) {
            $this->artifact = ChatArtifact::where('uuid', $artifactUuid)->first();
        }
    }

    /**
     * The download itself happens entirely client-side: this just hands the
     * artifact's stored text content back to Alpine, which builds a Blob
     * (or a JSZip archive when the artifact `type` is `zip`) and triggers a
     * native browser download — no server-generated file, no temp storage.
     */
    public function render()
    {
        return view('livewire.chat.split-view-panel');
    }
}
