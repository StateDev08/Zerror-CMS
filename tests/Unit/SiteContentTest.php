<?php

namespace Tests\Unit;

use App\Support\SiteContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_footer_pages_parse_label_slug_lines(): void
    {
        set_setting('footer_pages', "Impressum|impressum\nDatenschutz|datenschutz\n# comment\n");

        $pages = SiteContent::footerPages();

        $this->assertSame([
            'impressum' => 'Impressum',
            'datenschutz' => 'Datenschutz',
        ], $pages);
    }

    public function test_footer_credit_can_be_disabled(): void
    {
        set_setting('footer_credit_enabled', '0');
        set_setting('footer_credit', 'Hidden Credit');

        $this->assertSame('', SiteContent::footerCredit());
    }

    public function test_music_dock_defaults(): void
    {
        $this->assertTrue(SiteContent::musicDockEnabled());
        $this->assertSame('Clan Radio', SiteContent::musicDockTitle());
        $this->assertSame('Now Playing · Ambient', SiteContent::musicDockSubtitle());
    }

    public function test_donation_url_prefers_setting(): void
    {
        set_setting('donation_url', 'https://example.com/donate');

        $this->assertSame('https://example.com/donate', SiteContent::donationUrl());
    }
}
