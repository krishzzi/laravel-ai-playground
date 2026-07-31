<?php

namespace App\Http\Controllers\Api\V1;

use App\Ai\Agents\VoiceAgent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VoiceController extends Controller
{
    public function speak(Request $request, VoiceAgent $agent)
    {
        $data = $request->validate([
            'text' => 'required|string|max:8000',
            'provider' => 'required|string',
            'model' => 'required|string',
            'voice' => 'nullable|string',
        ]);

        return response()->json($agent->speak($request->user(), $data['text'], $data['provider'], $data['model'], $data['voice'] ?? 'default'));
    }

    public function transcribe(Request $request, VoiceAgent $agent)
    {
        $data = $request->validate([
            'audio' => 'required|file|max:51200',
            'provider' => 'required|string',
            'model' => 'required|string',
            'diarize' => 'nullable|boolean',
        ]);

        return response()->json($agent->transcribe($request->user(), $request->file('audio'), $data['provider'], $data['model'], (bool) ($data['diarize'] ?? false)));
    }
}
