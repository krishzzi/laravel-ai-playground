<?php

use Illuminate\Support\Facades\Route;
use Laravel\Ai\Responses\StreamedAgentResponse;

Route::get('/', function () {
    return view('welcome');
});


// Ai
//Route::prefix('chat')->group(function (){
//    Route::get('/',[\App\Http\Controllers\Ai\ChatController::class,'index'])->name('chat');
//    Route::post('inference',[\App\Http\Controllers\Ai\ChatController::class,'inference'])->name('chat.inference');
//    Route::get('stream',[\App\Http\Controllers\Ai\ChatController::class,'stream'])->name('chat.stream');
//});



Route::get('chat',\App\Livewire\ChatPage::class)->name('chat.index');



//
//Route::get('ai-chat',function (){
//
//    $response = (new \App\Ai\Agents\MasterAgent())->prompt('What is Laravel? what is latest laravel version you know?');
//
//    return response()->json([
//       'response' => $response
//    ]);
//
//});
//
//Route::get('ai-chat/stream',function (){
//
//    $response =  (new \App\Ai\Agents\ChatAgent)->stream('What is Laravel? what is latest laravel version you know?');
//    dd($response);
//    return $response;
//
//});



