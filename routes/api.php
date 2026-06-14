<?php

use App\Http\Controllers\Api\BotController;
use Illuminate\Support\Facades\Route;

Route::middleware('bot.token')->prefix('bot')->group(function (): void {
    Route::get('/settings', [BotController::class, 'settings']);
    Route::get('/user', [BotController::class, 'user']);
    Route::post('/users/link', [BotController::class, 'linkUser']);
    Route::post('/messages', [BotController::class, 'storeMessage']);
});

Route::post('/webhook/whatsapp', [BotController::class, 'storeMessage'])->middleware('bot.token');
