<?php

namespace App\Http\Controllers\Api\V1;

use App\Ai\Agents\ImageAgent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageGenerationController extends Controller
{
    public function generate(Request $request, ImageAgent $agent)
    {
        $data = $request->validate([
            'prompt' => 'required|string|max:4000',
            'provider' => 'required|string',
            'model' => 'required|string',
            'aspect' => 'nullable|in:square,portrait,landscape',
            'references.*' => 'nullable|image|max:10240',
        ]);

        $result = $agent->generate(
            $request->user(),
            $data['prompt'],
            $data['provider'],
            $data['model'],
            $request->file('references', []),
            $data['aspect'] ?? 'square',
        );

        return response()->json($result);
    }
}
