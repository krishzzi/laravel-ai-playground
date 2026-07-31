<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ModelCatalogService;
use Illuminate\Http\Request;

class ModelCatalogController extends Controller
{
    public function __invoke(Request $request, ModelCatalogService $catalog)
    {
        return [
            'text' => $catalog->availableTextModels($request->user()),
            'image' => $catalog->forFeature('image'),
            'tts' => $catalog->forFeature('tts'),
            'stt' => $catalog->forFeature('stt'),
            'thinking_modes' => $catalog->thinkingModes(),
        ];
    }
}
