<?php

namespace Tests\Feature;

use App\Models\MarketplaceCategory;
use App\Models\MarketplaceListing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_create_form(): void
    {
        $this->get(route('marketplace.create'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_listing(): void
    {
        $user = User::factory()->create();
        $category = MarketplaceCategory::create([
            'name' => 'Items',
            'slug' => 'items',
            'order' => 0,
        ]);

        $this->actingAs($user)
            ->post(route('marketplace.store'), [
                'category_id' => $category->id,
                'title' => 'Seltene Spitzhacke',
                'description' => 'Gut erhalten',
                'price_type' => 'fixed',
                'price_value' => 12.5,
                'contact_info' => 'ingame: Player',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('marketplace_listings', [
            'title' => 'Seltene Spitzhacke',
            'user_id' => $user->id,
            'published' => true,
        ]);
    }

    public function test_owner_can_edit_own_listing(): void
    {
        $user = User::factory()->create();
        $listing = MarketplaceListing::create([
            'user_id' => $user->id,
            'title' => 'Alte Anzeige',
            'slug' => 'alte-anzeige',
            'description' => 'Text',
            'price_type' => 'free',
            'published' => true,
        ]);

        $this->actingAs($user)
            ->get(route('marketplace.edit', $listing))
            ->assertOk();
    }
}
