<?php

use App\Http\Controllers\Api\DiscordBotController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Discord Bot API (geschützt durch X-API-Key)
|--------------------------------------------------------------------------
*/
Route::middleware(['discord.bot.api'])->prefix('discord-bot')->group(function () {
    Route::get('commands', [DiscordBotController::class, 'commands']);
    Route::post('commands/run', [DiscordBotController::class, 'runCommand']);
    Route::get('player', [DiscordBotController::class, 'playerByDiscordId']);
    Route::post('link', [DiscordBotController::class, 'linkDiscord']);
});
