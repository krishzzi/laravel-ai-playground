<?php


use App\Http\Controllers\Chat\StreamController;
use App\Models\ChatSession;
use Illuminate\Support\Facades\Route;

// V2 [Current]
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/chat/new', function () {
        $session = auth()->user()->chatSessions()->create(['title' => 'New Chat']);

        return redirect()->route('chat.show', $session);
    })->name('chat.new');

    Route::get('/chat/{session:uuid}', function (ChatSession $session) {
        abort_unless($session->user_id === auth()->id(), 403);

        return view('chat.show', ['session' => $session]);
    })->name('chat.show');

    // SSE-style streamed completion for a given pending message.
    Route::post('/chat/{session:uuid}/stream', StreamController::class)->name('chat.stream');

    Route::view('/studio/image', 'studio.image')->name('studio.image');
    Route::view('/studio/video', 'studio.video')->name('studio.video');
    Route::view('/tools', 'tools.index')->name('tools.index');
    Route::view('/skills', 'skills.index')->name('skills.index');
    Route::view('/settings/providers', 'settings.providers')->name('settings.providers');
    Route::view('/billing/gems', 'billing.gems')->name('billing.gems');
    Route::view('/mcp/servers', 'mcp.servers')->name('mcp.servers');
});



// Common
Route::get('/', function () {
    return view('welcome');
});



// V1 [OLD Not For this Branch]




Route::get('simple-chat',\App\Livewire\SimpleChat::class);

Route::get('ai-chat',function (){

    $response = (new \App\Ai\Agents\OllamaAgent)->prompt('What is Laravel? what is latest laravel version you know?');
    dd((string) $response);
    return response()->json([
        'response' => $response
    ]);

});
