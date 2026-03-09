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

    /** Discord-ID mit User verknüpfen (z. B. nach /link im Discord). */
    public function linkDiscord(Request $request): JsonResponse
    {
        $discordId = $request->input('discord_id');
        $userId = $request->input('user_id');
        $token = $request->input('link_token'); // Optional: einmaliger Token aus UserCP

        if (! $discordId) {
            return response()->json(['error' => 'discord_id required'], 400);
        }

        // Wenn user_id + optional link_token: Verknüpfung von Website aus
        if ($userId) {
            $user = User::find($userId);
            if (! $user) {
                return response()->json(['error' => 'user not found'], 404);
            }
            $user->update(['discord_id' => $discordId]);
            return response()->json(['success' => true, 'message' => 'Discord verknüpft']);
        }

        return response()->json(['error' => 'user_id or link_token required'], 400);
    }
}
