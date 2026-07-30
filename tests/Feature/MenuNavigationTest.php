<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\Post;
use App\Support\MenuTargets;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_targets_include_cms_modules_and_pages(): void
    {
        Post::create([
            'title' => 'Über uns',
            'slug' => 'ueber-uns',
            'content' => 'Hallo',
            'type' => 'page',
            'published' => true,
        ]);

        $options = MenuTargets::flatOptions();

        $this->assertArrayHasKey('home', $options);
        $this->assertArrayHasKey('news.index', $options);
        $this->assertArrayHasKey('forum.index', $options);
        $this->assertArrayHasKey('/page/impressum', $options);
        $this->assertArrayHasKey('/page/ueber-uns', $options);
        $this->assertSame('Über uns', $options['/page/ueber-uns']);
        $this->assertTrue(MenuTargets::isKnown('news.index'));
        $this->assertFalse(MenuTargets::isKnown('https://example.com'));
        $this->assertArrayHasKey('top', MenuTargets::positions());
    }

    public function test_menu_item_resolves_route_names_and_page_paths(): void
    {
        $home = MenuItem::create([
            'position' => 'top',
            'label' => 'Home',
            'link' => 'home',
            'sort_order' => 0,
            'is_visible' => true,
        ]);
        $page = MenuItem::create([
            'position' => 'top',
            'label' => 'Impressum',
            'link' => '/page/impressum',
            'sort_order' => 10,
            'is_visible' => true,
        ]);
        $external = MenuItem::create([
            'position' => 'top',
            'label' => 'Discord',
            'link' => 'https://discord.gg/example',
            'sort_order' => 20,
            'is_visible' => true,
        ]);

        $this->assertSame(url('/'), $home->resolved_url);
        $this->assertSame(url('/page/impressum'), $page->resolved_url);
        $this->assertSame('https://discord.gg/example', $external->resolved_url);
    }

    public function test_homepage_top_nav_renders_menu_items_from_database(): void
    {
        MenuItem::create([
            'position' => 'top',
            'label' => 'CustomTopLink',
            'link' => 'news.index',
            'sort_order' => 0,
            'is_visible' => true,
        ]);
        MenuItem::create([
            'position' => 'top',
            'label' => 'HiddenTop',
            'link' => 'wiki.index',
            'sort_order' => 5,
            'is_visible' => false,
        ]);

        $response = $this->get(route('home'));
        $response->assertOk();
        $response->assertSee('CustomTopLink', false);
        $response->assertDontSee('HiddenTop', false);
    }

    public function test_menu_item_seeder_creates_top_position_entries(): void
    {
        $this->seed(\Database\Seeders\MenuItemSeeder::class);

        $this->assertDatabaseHas('menu_items', [
            'position' => 'top',
            'link' => 'home',
        ]);
        $this->assertDatabaseHas('menu_items', [
            'position' => 'top',
            'link' => 'forum.index',
        ]);
        $this->assertGreaterThanOrEqual(7, MenuItem::position('top')->count());
    }
}
