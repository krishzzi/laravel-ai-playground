<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Ai\Responses\StreamableAgentResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        return view('ai.chat_index');
    }

    public function inference(Request $request)
    {
        $validated = $request->validate([
            'message' => ['required_without_all:attachments,audio', 'nullable', 'string', 'max:12000'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt,md,webp'],
            'audio' => ['nullable', 'file', 'max:10240', 'mimes:mp3,wav,webm,m4a,ogg'],
        ]);

        $message = trim($validated['message'] ?? '');

        $prompt = $message;

        if ($request->hasFile('attachments')) {
            $attachmentNames = collect($request->file('attachments'))
                ->map(fn ($file) => $file->getClientOriginalName())
                ->values()
                ->all();

            $prompt .= "\n\nAttachments:\n" . implode("\n", $attachmentNames);
        }

        if ($request->hasFile('audio')) {
            $prompt .= "\n\nAudio attached: " . $request->file('audio')->getClientOriginalName();
        }

        if ($request->expectsJson()) {
            $response = (new \App\Ai\Agents\MasterAgent)->prompt($prompt);

            return response()->json([
                'ok' => true,
                'response' => $response->text ?? (string) $response,
            ]);
        }

        return (new \App\Ai\Agents\MasterAgent)->stream($prompt);
    }

    public function stream(Request $request)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:12000'],
        ]);

        $prompt = $validated['message'];

        return (new \App\Ai\Agents\ChatAgent)->stream($prompt);
    }



}
