<?php

namespace Tests\Feature;

use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_subscribe_to_newsletter(): void
    {
        $this->post(route('newsletter.subscribe'), [
            'email' => 'clan@example.com',
        ])->assertRedirect();

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'clan@example.com',
        ]);

        $subscriber = NewsletterSubscriber::where('email', 'clan@example.com')->first();
        $this->assertNotNull($subscriber?->confirmed_at);
        $this->assertNotEmpty($subscriber?->token);
    }

    public function test_user_can_unsubscribe_with_token(): void
    {
        $subscriber = NewsletterSubscriber::create([
            'email' => 'leave@example.com',
            'token' => 'unsubscribe-token-123',
            'confirmed_at' => now(),
        ]);

        $this->get(route('newsletter.unsubscribe', $subscriber->token))
            ->assertOk();

        $this->assertDatabaseMissing('newsletter_subscribers', [
            'email' => 'leave@example.com',
        ]);
    }
}
