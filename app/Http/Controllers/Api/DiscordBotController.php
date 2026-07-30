<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiscordQuickCommand;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiscordBotController extends Controller
{
    /** Liste aller öffentlichen + optional nutzerspezifischen Quick-Commands für Slash-Command-Registrierung / Abruf. */
    public function commands(Request $request): JsonResponse
    {
        $commands = DiscordQuickCommand::where('is_public', true)
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'response_text', 'use_count']);

        return response()->json(['commands' => $commands]);
    }

    /** Einzelnen Befehl ausführen (Name angeben), erhöht use_count und liefert response_text. */
    public function runCommand(Request $request): JsonResponse
    {
        $name = DiscordQuickCommand::normalizeName((string) $request->input('name', ''));
        if ($name === '') {
            return response()->json(['error' => 'name required'], 400);
        }

        $command = DiscordQuickCommand::where('name', $name)->where('is_public', true)->first();
        if (! $command) {
            return response()->json(['error' => 'command not found', 'response_text' => null], 404);
        }

        $command->incrementUseCount();

        return response()->json([
            'name' => $command->name,
            'response_text' => $command->response_text,
        ]);
    }

    /** Spieler anhand Discord-ID abrufen (für Verknüpfung Website ↔ Discord). */
    public function playerByDiscordId(Request $request): JsonResponse
    {
        $discordId = $request->input('discord_id');
        if (! $discordId) {
            return response()->json(['error' => 'discord_id required'], 400);
        }

        $user = User::where('discord_id', $discordId)->first();
        if (! $user) {
            return response()->json(['found' => false, 'user' => null]);
        }

        return response()->json([
            'found' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'discord_handle' => $user->discord_handle,
            ],
        ]);
    }

    /** Discord-ID mit User verknüpfen (nur mit gültigem Link-Token aus UserCP). */
    public function linkDiscord(Request $request): JsonResponse
    {
        $discordId = $request->input('discord_id');
        $token = $request->input('link_token');

        if (! $discordId) {
            return response()->json(['error' => 'discord_id required'], 400);
        }

        if (! $token || ! is_string($token)) {
            return response()->json(['error' => 'link_token required'], 400);
        }

        $user = User::where('discord_link_token', $token)
            ->where('discord_link_token_expires_at', '>', now())
            ->first();

        if (! $user) {
            return response()->json(['error' => 'invalid or expired link_token'], 404);
        }

        $data = [
            'discord_id' => (string) $discordId,
            'discord_link_token' => null,
            'discord_link_token_expires_at' => null,
        ];

        if ($request->filled('discord_handle')) {
            $data['discord_handle'] = (string) $request->input('discord_handle');
        }

        $user->update($data);

        return response()->json(['success' => true, 'message' => 'Discord verknüpft']);
    }
}
