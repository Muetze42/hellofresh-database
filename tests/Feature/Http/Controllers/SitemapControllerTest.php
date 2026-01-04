<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Country;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SitemapControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    #[Test]
    public function index_returns_xml_sitemap_index(): void
    {
        $country = Country::factory()->create([
            'code' => 'US',
            'locales' => ['en'],
        ]);

        Storage::disk('public')->put('sitemaps/sitemap-en-US.xml', '<?xml version="1.0"?><urlset></urlset>');

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml');
        $this->assertStringContainsString('max-age=3600', (string) $response->headers->get('Cache-Control'));
    }

    #[Test]
    public function index_returns_valid_sitemap_index_structure(): void
    {
        $country = Country::factory()->create([
            'code' => 'DE',
            'locales' => ['de'],
        ]);

        Storage::disk('public')->put('sitemaps/sitemap-de-DE.xml', '<?xml version="1.0"?><urlset></urlset>');

        $response = $this->get('/sitemap.xml');

        $content = $response->getContent();
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', (string) $content);
        $this->assertStringContainsString('<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', (string) $content);
        $this->assertStringContainsString('</sitemapindex>', (string) $content);
    }

    #[Test]
    public function index_includes_existing_sitemap_files(): void
    {
        $country = Country::factory()->create([
            'code' => 'FR',
            'locales' => ['fr'],
        ]);

        Storage::disk('public')->put('sitemaps/sitemap-fr-FR.xml', '<?xml version="1.0"?><urlset></urlset>');

        $response = $this->get('/sitemap.xml');

        $content = $response->getContent();
        $this->assertStringContainsString('<sitemap>', (string) $content);
        $this->assertStringContainsString('sitemap/fr-FR.xml', (string) $content);
        $this->assertStringContainsString('<lastmod>', (string) $content);
    }

    #[Test]
    public function index_skips_non_existent_sitemap_files(): void
    {
        $country = Country::factory()->create([
            'code' => 'IT',
            'locales' => ['it', 'en'],
        ]);

        Storage::disk('public')->put('sitemaps/sitemap-it-IT.xml', '<?xml version="1.0"?><urlset></urlset>');

        $response = $this->get('/sitemap.xml');

        $content = $response->getContent();
        $this->assertStringContainsString('sitemap/it-IT.xml', (string) $content);
        $this->assertStringNotContainsString('sitemap/en-IT.xml', (string) $content);
    }

    #[Test]
    public function index_lists_multiple_countries(): void
    {
        $country1 = Country::factory()->create(['code' => 'AT', 'locales' => ['de']]);
        $country2 = Country::factory()->create(['code' => 'CH', 'locales' => ['de']]);

        Storage::disk('public')->put('sitemaps/sitemap-de-AT.xml', '<?xml version="1.0"?><urlset></urlset>');
        Storage::disk('public')->put('sitemaps/sitemap-de-CH.xml', '<?xml version="1.0"?><urlset></urlset>');

        $response = $this->get('/sitemap.xml');

        $content = $response->getContent();
        $this->assertStringContainsString('sitemap/de-AT.xml', (string) $content);
        $this->assertStringContainsString('sitemap/de-CH.xml', (string) $content);
    }

    #[Test]
    public function index_orders_countries_by_code(): void
    {
        $countryZ = Country::factory()->create(['code' => 'ZA', 'locales' => ['en']]);
        $countryA = Country::factory()->create(['code' => 'AU', 'locales' => ['en']]);

        Storage::disk('public')->put('sitemaps/sitemap-en-ZA.xml', '<?xml version="1.0"?><urlset></urlset>');
        Storage::disk('public')->put('sitemaps/sitemap-en-AU.xml', '<?xml version="1.0"?><urlset></urlset>');

        $response = $this->get('/sitemap.xml');

        $content = $response->getContent();
        $auPos = strpos((string) $content, 'en-AU.xml');
        $zaPos = strpos((string) $content, 'en-ZA.xml');

        $this->assertLessThan($zaPos, $auPos);
    }

    #[Test]
    public function index_includes_multiple_locales_per_country(): void
    {
        $country = Country::factory()->create([
            'code' => 'BE',
            'locales' => ['nl', 'fr', 'de'],
        ]);

        Storage::disk('public')->put('sitemaps/sitemap-nl-BE.xml', '<?xml version="1.0"?><urlset></urlset>');
        Storage::disk('public')->put('sitemaps/sitemap-fr-BE.xml', '<?xml version="1.0"?><urlset></urlset>');
        Storage::disk('public')->put('sitemaps/sitemap-de-BE.xml', '<?xml version="1.0"?><urlset></urlset>');

        $response = $this->get('/sitemap.xml');

        $content = $response->getContent();
        $this->assertStringContainsString('sitemap/nl-BE.xml', (string) $content);
        $this->assertStringContainsString('sitemap/fr-BE.xml', (string) $content);
        $this->assertStringContainsString('sitemap/de-BE.xml', (string) $content);
    }

    #[Test]
    public function index_returns_empty_index_when_no_sitemaps_exist(): void
    {
        Country::factory()->create(['code' => 'NO', 'locales' => ['no']]);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $content = $response->getContent();
        $this->assertStringContainsString('<sitemapindex', (string) $content);
        $this->assertStringContainsString('</sitemapindex>', (string) $content);
        $this->assertStringNotContainsString('<sitemap>', (string) $content);
    }

    #[Test]
    public function show_returns_sitemap_content(): void
    {
        $sitemapContent = '<?xml version="1.0"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>';
        Storage::disk('public')->put('sitemaps/sitemap-en-GB.xml', $sitemapContent);

        $response = $this->get('/sitemap/en-GB.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml');
        $this->assertStringContainsString('max-age=3600', (string) $response->headers->get('Cache-Control'));
        $this->assertEquals($sitemapContent, $response->getContent());
    }

    #[Test]
    public function show_returns_404_for_non_existent_sitemap(): void
    {
        $response = $this->get('/sitemap/xx-XX.xml');

        $response->assertNotFound();
    }

    #[Test]
    public function show_returns_correct_sitemap_for_locale_country(): void
    {
        $contentDE = '<?xml version="1.0"?><urlset><url><loc>https://de.example.com</loc></url></urlset>';
        $contentEN = '<?xml version="1.0"?><urlset><url><loc>https://en.example.com</loc></url></urlset>';

        Storage::disk('public')->put('sitemaps/sitemap-de-DE.xml', $contentDE);
        Storage::disk('public')->put('sitemaps/sitemap-en-DE.xml', $contentEN);

        $responseDE = $this->get('/sitemap/de-DE.xml');
        $responseEN = $this->get('/sitemap/en-DE.xml');

        $this->assertEquals($contentDE, $responseDE->getContent());
        $this->assertEquals($contentEN, $responseEN->getContent());
    }

    #[Test]
    public function index_formats_lastmod_correctly(): void
    {
        $country = Country::factory()->create([
            'code' => 'ES',
            'locales' => ['es'],
        ]);

        Storage::disk('public')->put('sitemaps/sitemap-es-ES.xml', '<?xml version="1.0"?><urlset></urlset>');

        $response = $this->get('/sitemap.xml');

        $content = $response->getContent();
        $this->assertMatchesRegularExpression('/<lastmod>\d{4}-\d{2}-\d{2}<\/lastmod>/', (string) $content);
    }
}
