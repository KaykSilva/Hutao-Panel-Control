<?php

use App\Http\Controllers\Api\BotController;
use Illuminate\Support\Facades\Route;

Route::middleware('bot.token')->prefix('bot')->group(function (): void {
    Route::get('/settings', [BotController::class, 'settings']);
    Route::get('/user', [BotController::class, 'user']);
    Route::get('/identity', [BotController::class, 'identity']);
    Route::get('/debts', [BotController::class, 'debts']);
    Route::post('/users/link', [BotController::class, 'linkUser']);
    Route::post('/groups/sync', [BotController::class, 'syncGroup']);
    Route::post('/messages', [BotController::class, 'storeMessage']);
});

Route::post('/webhook/whatsapp', [BotController::class, 'storeMessage'])->middleware('bot.token');
