<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_welcome_settings(): void
    {
        set_setting('home_welcome_title', 'Clan Willkommenstest');
        set_setting('home_welcome_text', 'Das ist unsere Startseiten-Beschreibung.');
        set_setting('home_show_cta', '1');

        $response = $this->get(route('home'));
        $response->assertOk();
        $response->assertSee('Clan Willkommenstest', false);
        $response->assertSee('Das ist unsere Startseiten-Beschreibung.', false);
        $response->assertSee(__('nav.apply'), false);
        $response->assertSee(__('nav.news'), false);
    }

    public function test_homepage_welcome_settings_can_be_saved_via_filament_form(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('admin');

        \Livewire\Livewire::actingAs($user)
            ->test(\App\Filament\Pages\SiteSettingsPage::class)
            ->fillForm([
                'home_welcome_title' => 'ACP Titel',
                'home_welcome_text' => '<p>ACP <strong>Beschreibung</strong></p>',
                'home_show_cta' => false,
            ])
            ->call('saveHomeSettings')
            ->assertHasNoFormErrors();

        $this->assertSame('ACP Titel', setting('home_welcome_title'));
        $this->assertStringContainsString('Beschreibung', (string) setting('home_welcome_text'));
        $this->assertSame('0', (string) setting('home_show_cta'));
    }

    public function test_site_settings_home_form_uses_cms_rich_editor(): void
    {
        $pageSource = file_get_contents(app_path('Filament/Pages/SiteSettingsPage.php'));
        $this->assertStringContainsString('CmsRichEditor::make(\'home_welcome_text\')', $pageSource);
        $this->assertStringNotContainsString('Textarea::make(\'home_welcome_text\')', $pageSource);
    }

    public function test_home_content_partial_is_welcome_only(): void
    {
        $path = base_path('themes/common/views/partials/home-content.blade.php');
        $this->assertFileExists($path);
        $contents = file_get_contents($path);
        $this->assertStringNotContainsString('wiki.', $contents);
        $this->assertStringNotContainsString('/wiki', $contents);
        $this->assertStringNotContainsString("slot('home')", $contents);
        $this->assertStringNotContainsString("slot('sidebar')", $contents);
        $this->assertStringContainsString('home-welcome', $contents);
    }

    public function test_home_widget_seeder_creates_default_instances(): void
    {
        $this->seed(\Database\Seeders\HomeWidgetSeeder::class);

        $this->assertDatabaseHas('widget_instances', [
            'slot' => 'left',
            'widget_key' => 'latest_news',
        ]);
        $this->assertDatabaseHas('widget_instances', [
            'slot' => 'left',
            'widget_key' => 'upcoming_events',
        ]);
    }

    public function test_layouts_use_global_widget_columns(): void
    {
        $shell = base_path('themes/common/views/partials/site-shell.blade.php');
        $this->assertFileExists($shell);
        $contents = file_get_contents($shell);
        $this->assertStringContainsString("slot('left')", $contents);
        $this->assertStringContainsString("slot('right')", $contents);
        $this->assertStringContainsString('global-widgets-left panel-box', $contents);
        $this->assertStringContainsString('global-widgets-main clan-frame', $contents);

        $bluebyte = file_get_contents(base_path('themes/bluebyte/views/layouts/app.blade.php'));
        $this->assertStringContainsString("theme::partials.site-shell", $bluebyte);
    }
}
