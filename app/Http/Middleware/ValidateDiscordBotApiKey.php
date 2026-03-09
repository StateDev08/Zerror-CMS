<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateDiscordBotApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = config('discord_bot.api_key');
        if (! $key) {
            return response()->json(['error' => 'Discord bot API not configured'], 503);
        }
        $provided = $request->header('X-API-Key') ?? $request->query('api_key');
        if ($provided !== $key) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $next($request);
    }
}
