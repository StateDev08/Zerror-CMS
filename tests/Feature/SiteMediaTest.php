<?php

namespace Tests\Feature;

use App\Models\SliderSlide;
use App\Models\User;
use App\Support\SiteMedia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiteMediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_logo_upload_stores_setting_and_resolves_url(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();
        $file = UploadedFile::fake()->image('logo.png', 120, 40);

        $this->actingAs($admin)
            ->post(route('filament.admin.site-media'), [
                'logo' => $file,
                'site_name' => 'ZerroCMS',
            ])
            ->assertRedirect();

        $path = setting('site_logo');
        $this->assertNotEmpty($path);
        Storage::disk('public')->assertExists($path);
        $this->assertNotNull(SiteMedia::logoUrl());
        $this->assertStringContainsString('app-storage/', (string) SiteMedia::logoUrl());
    }

    public function test_banner_is_disabled_when_slider_becomes_active(): void
    {
        Storage::fake('public');
        set_setting('site_banner', 'site/banner.png');
        set_setting('site_banner_enabled', '1');
        Storage::disk('public')->put('site/banner.png', 'fake');

        SliderSlide::create([
            'title' => 'Slide',
            'image' => 'slider/a.png',
            'order' => 0,
            'active' => true,
        ]);

        $this->assertSame('0', (string) setting('site_banner_enabled'));
        $this->assertFalse(SiteMedia::bannerEnabled());
    }

    public function test_enforce_exclusivity_disables_banner_when_both_active(): void
    {
        Storage::fake('public');
        set_setting('site_banner', 'site/banner.png');
        set_setting('site_banner_enabled', '1');
        Storage::disk('public')->put('site/banner.png', 'fake');

        SliderSlide::withoutEvents(function () {
            SliderSlide::create([
                'title' => 'Slide',
                'image' => 'slider/a.png',
                'order' => 0,
                'active' => true,
            ]);
        });
        set_setting('site_banner_enabled', '1');

        $this->assertTrue(SiteMedia::bannerAndSliderConflict());
        $result = SiteMedia::enforceBannerSliderExclusivity();
        $this->assertTrue($result['resolved']);
        $this->assertFalse(SiteMedia::bannerEnabled());
    }
}
