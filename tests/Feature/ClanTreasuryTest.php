<?php

namespace Tests\Feature;

use App\Models\ClanTreasuryCategory;
use App\Models\ClanTreasuryEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClanTreasuryTest extends TestCase
{
    use RefreshDatabase;

    public function test_treasury_page_is_reachable(): void
    {
        $this->get(route('clan-treasury.index'))
            ->assertOk();
    }

    public function test_treasury_page_shows_entries(): void
    {
        $category = ClanTreasuryCategory::create([
            'name' => 'Spenden',
            'order' => 0,
        ]);

        ClanTreasuryEntry::create([
            'type' => 'income',
            'amount' => 50.00,
            'clan_treasury_category_id' => $category->id,
            'title' => 'Monatsspende',
            'entry_date' => now()->toDateString(),
        ]);

        $this->get(route('clan-treasury.index'))
            ->assertOk()
            ->assertSee('Monatsspende')
            ->assertSee('Spenden');
    }
}
