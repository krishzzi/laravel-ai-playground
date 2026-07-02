<?php

use App\Livewire\Chat\ChatApp;
use Illuminate\Support\Facades\Route;
use Laravel\Ai\Responses\StreamedAgentResponse;

Route::get('/', function () {
    return view('welcome');
});




// ── Redirect root to chat ──────────────────────────────────
//Route::redirect('/', '/chat');

// ── Main chat routes ───────────────────────────────────────
//Route::get('/chat', ChatApp::class)->name('chat');
//Route::get('/chat/{conversation}', ChatApp::class)->name('chat.show');


Route::get('simple-chat',\App\Livewire\SimpleChat::class);






// Ai
//Route::prefix('chat')->group(function (){
//    Route::get('/',[\App\Http\Controllers\Ai\ChatController::class,'index'])->name('chat');
//    Route::post('inference',[\App\Http\Controllers\Ai\ChatController::class,'inference'])->name('chat.inference');
//    Route::get('stream',[\App\Http\Controllers\Ai\ChatController::class,'stream'])->name('chat.stream');
//});



//Route::get('chat',\App\Livewire\ChatPage::class)->name('chat.index');



//
Route::get('ai-chat',function (){

    $response = (new \App\Ai\Agents\OllamaAgent)->prompt('What is Laravel? what is latest laravel version you know?');
    dd((string) $response);
    return response()->json([
       'response' => $response
    ]);

});
//
//Route::get('ai-chat/stream',function (){
//
//    $response =  (new \App\Ai\Agents\ChatAgent)->stream('What is Laravel? what is latest laravel version you know?');
//    dd($response);
//    return $response;
//
//});



