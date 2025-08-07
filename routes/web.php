<?php

use App\Http\Controllers\BrochureController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DebateController;
use App\Http\Controllers\PrismAiController;
use App\Http\Controllers\RawMultipleAiController;
use App\Http\Controllers\StreamController;
use Illuminate\Support\Facades\Route;

Route::get('/')
    ->uses([Controller::class, 'index'])
    ->name('auth.home');
Route::get('/brochure', [BrochureController::class, 'index'])->name('home');
Route::post('/generate', [BrochureController::class, 'generate'])->name('generate.brochure');
Route::get('/brochure/download/{index}', [BrochureController::class, 'download'])->name('brochure.download');
Route::get('/stream', [StreamController::class, 'index'])->name('stream.index');
Route::get('/stream/run', [StreamController::class, 'stream'])->name('stream');
Route::get('/ai/raw', RawMultipleAiController::class);
Route::get('/ai/prism', PrismAiController::class);
Route::get('/ai-debate', [DebateController::class, 'index']);
Route::get('/ai-debate/stream', [DebateController::class, 'stream']);
Route::post('/chat', [ChatController::class, 'send']);
Route::view('/chat', 'chat.chat');
Route::post('/chat-stream-run', [ChatController::class, 'chatStream'])->name('chat.stream');
Route::view('/chat-stream', 'chat.chat-stream');

Route::view('/chat-tools', 'chat.chat-tools');
Route::post('/chat-tools-run', [ChatController::class, 'chatWithTools']);

Route::view('/chat-mcp', 'chat.chat-mcp');
Route::post('/chat-mcp-run', [ChatController::class, 'chatWithMCP']);

Route::view('/chat/audio', 'chat.chat-audio');
Route::view('/chat/image', 'chat.chat-image');
Route::post('/api/chat/audio', [ChatController::class, 'chatAudio']);
Route::post('/api/chat/image', [ChatController::class, 'chatImage']);
