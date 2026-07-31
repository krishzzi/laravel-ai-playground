<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ChatMessageController;
use App\Http\Controllers\Api\V1\ChatSessionController;
use App\Http\Controllers\Api\V1\GemWalletController;
use App\Http\Controllers\Api\V1\ImageGenerationController;
use App\Http\Controllers\Api\V1\ModelCatalogController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\SkillController;
use App\Http\Controllers\Api\V1\ToolController;
use App\Http\Controllers\Api\V1\VoiceController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




/**
 * Versioned, Sanctum-protected API surface. This is what the future native
 * Android/iOS app (or any third-party client) talks to — the Livewire UI is
 * one consumer of the same underlying services, not a separate code path.
 */
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    Route::apiResource('projects', ProjectController::class);

    Route::apiResource('chat-sessions', ChatSessionController::class);
    Route::post('chat-sessions/{chatSession}/messages', [ChatMessageController::class, 'store']);
    Route::get('chat-sessions/{chatSession}/messages', [ChatMessageController::class, 'index']);
    Route::post('chat-sessions/{chatSession}/messages/{message}/branch', [ChatMessageController::class, 'branch']);

    Route::get('models', ModelCatalogController::class);

    Route::post('images/generate', [ImageGenerationController::class, 'generate']);
    Route::post('voice/speak', [VoiceController::class, 'speak']);
    Route::post('voice/transcribe', [VoiceController::class, 'transcribe']);

    Route::apiResource('tools', ToolController::class);
    Route::apiResource('skills', SkillController::class);

    Route::get('wallet', [GemWalletController::class, 'show']);
    Route::get('wallet/transactions', [GemWalletController::class, 'transactions']);
});
