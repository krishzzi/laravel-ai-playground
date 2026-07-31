<?php

namespace App\Http\Controllers\Chat;

use App\Ai\Agents\ChatAgent;
use App\Exceptions\InsufficientGemsException;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Skill;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Ai\Enums\Lab;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StreamController
{
    public function __invoke(Request $request, ChatSession $session): StreamedResponse
    {
        abort_unless($session->user_id === $request->user()->id, 403);

        $userMessage = ChatMessage::findOrFail($request->integer('message_id'));

        $tools = Tool::availableTo($request->user())->whereIn('slug', $request->array('tool_slugs'))->get();
        $skills = Skill::availableTo($request->user())->whereIn('slug', $request->array('skill_slugs'))->get();

        $agent = new ChatAgent($session, $tools, $skills);

        return response()->stream(function () use ($agent, $session, $userMessage) {
            $buffer = '';
            $assistantMessage = $session->messages()->create(['role' => 'assistant', 'content' => '']);

            try {
                $stream = $agent->stream(
                    '', // messages() already supplies full history including the just-created user turn
                    provider: [$session->default_provider, Lab::Anthropic->value], // simple failover chain
                    model: $session->default_model,
                );

                foreach ($stream as $event) {
                    if (method_exists($event, 'delta') && $event->delta()) {
                        $buffer .= $event->delta();
                        echo $event->delta();
                        ob_flush();
                        flush();
                    }
                }
            } catch (InsufficientGemsException $e) {
                echo "\n\n> ⚠️ ".$e->getMessage();
            } finally {
                $assistantMessage->update([
                    'content' => $buffer,
                    'provider' => $session->default_provider,
                    'model' => $session->default_model,
                    'thinking_mode' => $session->thinking_mode,
                ]);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
