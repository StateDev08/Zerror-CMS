<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class DiscordLinkApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('discord_bot.api_key', 'test-api-key');
    }

    public function test_link_requires_valid_token(): void
    {
        $this->postJson('/api/discord-bot/link', [
            'discord_id' => '1234567890',
            'link_token' => 'invalid',
        ], ['X-API-Key' => 'test-api-key'])
            ->assertNotFound()
            ->assertJson(['error' => 'invalid or expired link_token']);
    }

    public function test_link_succeeds_with_valid_token(): void
    {
        $user = User::factory()->create([
            'discord_link_token' => 'valid-link-token',
            'discord_link_token_expires_at' => now()->addMinutes(15),
        ]);

        $this->postJson('/api/discord-bot/link', [
            'discord_id' => '9876543210',
            'link_token' => 'valid-link-token',
            'discord_handle' => 'Player#0001',
        ], ['X-API-Key' => 'test-api-key'])
            ->assertOk()
            ->assertJson(['success' => true]);

        $user->refresh();
        $this->assertSame('9876543210', $user->discord_id);
        $this->assertSame('Player#0001', $user->discord_handle);
        $this->assertNull($user->discord_link_token);
    }

    public function test_player_lookup_by_discord_id(): void
    {
        User::factory()->create([
            'name' => 'Linked Player',
            'discord_id' => '111222333',
            'discord_handle' => 'Linked#1234',
        ]);

        $this->getJson('/api/discord-bot/player?discord_id=111222333', [
            'X-API-Key' => 'test-api-key',
        ])->assertOk()
            ->assertJson([
                'found' => true,
                'user' => [
                    'name' => 'Linked Player',
                    'discord_handle' => 'Linked#1234',
                ],
            ]);
    }
}
