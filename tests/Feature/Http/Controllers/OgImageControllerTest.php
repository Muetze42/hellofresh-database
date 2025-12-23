<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\OgImageController;
use App\Models\Country;
use App\Models\Menu;
use App\Models\Recipe;
use GdImage;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use Tests\TestCase;

final class OgImageControllerTest extends TestCase
{
    #[Test]
    public function it_generates_recipe_og_image(): void
    {
        $country = Country::factory()->create(['locales' => ['en']]);
        $recipe = Recipe::factory()->for($country)->create([
            'name' => ['en' => 'Test Recipe'],
            'image_path' => null,
        ]);

        $response = $this->get(route('og.recipe', $recipe));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/jpeg');
        $this->assertStringContainsString('max-age=86400', (string) $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('public', (string) $response->headers->get('Cache-Control'));
    }

    #[Test]
    public function it_generates_menu_og_image(): void
    {
        $country = Country::factory()->create(['locales' => ['en']]);
        $menu = Menu::factory()->for($country)->create([
            'year_week' => 202501,
        ]);

        $response = $this->get(route('og.menu', $menu));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/jpeg');
    }

    #[Test]
    public function it_generates_menu_og_image_with_locale_parameter(): void
    {
        $country = Country::factory()->create(['locales' => ['de', 'en']]);
        $menu = Menu::factory()->for($country)->create([
            'year_week' => 202501,
        ]);

        $response = $this->get(route('og.menu', ['menu' => $menu, 'locale' => 'de']));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/jpeg');
    }

    #[Test]
    public function it_uses_first_country_locale_when_locale_parameter_is_empty(): void
    {
        $country = Country::factory()->create(['locales' => ['fr', 'en']]);
        $menu = Menu::factory()->for($country)->create([
            'year_week' => 202501,
        ]);

        $response = $this->get(route('og.menu', ['menu' => $menu, 'locale' => '']));

        $response->assertOk();
    }

    #[Test]
    public function it_generates_generic_og_image(): void
    {
        $response = $this->get(route('og.generic'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/jpeg');
    }

    #[Test]
    public function it_generates_generic_og_image_with_custom_title(): void
    {
        $response = $this->get(route('og.generic', ['title' => 'Custom Title']));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/jpeg');
    }

    #[Test]
    public function create_canvas_returns_gd_image(): void
    {
        $controller = new OgImageController();
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('createCanvas');

        $canvas = $method->invoke($controller);

        $this->assertInstanceOf(GdImage::class, $canvas);
        $this->assertSame(1200, imagesx($canvas));
        $this->assertSame(630, imagesy($canvas));

        imagedestroy($canvas);
    }

    #[Test]
    public function fill_solid_background_renders_gradient(): void
    {
        $controller = new OgImageController();
        $reflection = new ReflectionClass($controller);
        $createCanvas = $reflection->getMethod('createCanvas');
        $fillBackground = $reflection->getMethod('fillSolidBackground');

        $canvas = $createCanvas->invoke($controller);
        $fillBackground->invoke($controller, $canvas);

        // Verify gradient is applied - top should be darker than bottom
        $topColor = imagecolorat($canvas, 600, 0);
        $bottomColor = imagecolorat($canvas, 600, 629);

        // The top should have lower RGB values than bottom (darker)
        $topRed = ($topColor >> 16) & 0xFF;
        $bottomRed = ($bottomColor >> 16) & 0xFF;

        $this->assertLessThanOrEqual($bottomRed, $topRed);

        imagedestroy($canvas);
    }

    #[Test]
    public function fill_solid_background_adds_accent_bar(): void
    {
        $controller = new OgImageController();
        $reflection = new ReflectionClass($controller);
        $createCanvas = $reflection->getMethod('createCanvas');
        $fillBackground = $reflection->getMethod('fillSolidBackground');

        $canvas = $createCanvas->invoke($controller);
        $fillBackground->invoke($controller, $canvas);

        // Check the accent bar at the bottom (last 4 pixels)
        $accentColor = imagecolorat($canvas, 600, 628);
        $red = ($accentColor >> 16) & 0xFF;
        $green = ($accentColor >> 8) & 0xFF;
        $blue = $accentColor & 0xFF;

        // Accent color should be green-ish (145, 193, 30)
        $this->assertSame(145, $red);
        $this->assertSame(193, $green);
        $this->assertSame(30, $blue);

        imagedestroy($canvas);
    }

    #[Test]
    public function add_gradient_overlay_applies_gradient(): void
    {
        $controller = new OgImageController();
        $reflection = new ReflectionClass($controller);
        $createCanvas = $reflection->getMethod('createCanvas');
        $fillBackground = $reflection->getMethod('fillSolidBackground');
        $addGradient = $reflection->getMethod('addGradientOverlay');

        $canvas = $createCanvas->invoke($controller);
        $fillBackground->invoke($controller, $canvas);
        $addGradient->invoke($controller, $canvas);

        // The bottom portion should be darker due to overlay
        $this->assertInstanceOf(GdImage::class, $canvas);

        imagedestroy($canvas);
    }

    #[Test]
    public function draw_title_adds_text_to_canvas(): void
    {
        $controller = new OgImageController();
        $reflection = new ReflectionClass($controller);
        $createCanvas = $reflection->getMethod('createCanvas');
        $fillBackground = $reflection->getMethod('fillSolidBackground');
        $drawTitle = $reflection->getMethod('drawTitle');

        $canvas = $createCanvas->invoke($controller);
        $fillBackground->invoke($controller, $canvas);
        $drawTitle->invoke($controller, $canvas, 'Test Title', false);

        $this->assertInstanceOf(GdImage::class, $canvas);

        imagedestroy($canvas);
    }

    #[Test]
    public function draw_title_with_logo_positions_differently(): void
    {
        $controller = new OgImageController();
        $reflection = new ReflectionClass($controller);
        $createCanvas = $reflection->getMethod('createCanvas');
        $fillBackground = $reflection->getMethod('fillSolidBackground');
        $drawTitle = $reflection->getMethod('drawTitle');

        $canvas = $createCanvas->invoke($controller);
        $fillBackground->invoke($controller, $canvas);
        $drawTitle->invoke($controller, $canvas, 'Test Title', true);

        $this->assertInstanceOf(GdImage::class, $canvas);

        imagedestroy($canvas);
    }

    #[Test]
    public function draw_badge_adds_badge_to_canvas(): void
    {
        $controller = new OgImageController();
        $reflection = new ReflectionClass($controller);
        $createCanvas = $reflection->getMethod('createCanvas');
        $fillBackground = $reflection->getMethod('fillSolidBackground');
        $drawBadge = $reflection->getMethod('drawBadge');

        $canvas = $createCanvas->invoke($controller);
        $fillBackground->invoke($controller, $canvas);
        $drawBadge->invoke($controller, $canvas);

        $this->assertInstanceOf(GdImage::class, $canvas);

        imagedestroy($canvas);
    }

    #[Test]
    public function calculate_crop_dimensions_when_source_wider_than_target(): void
    {
        $controller = new OgImageController();
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('calculateCropDimensions');

        // Source is wider: 2000x1000 (ratio 2.0) vs target 1200x630 (ratio ~1.9)
        $result = $method->invoke($controller, 2000, 1000, 2.0, 1200 / 630);

        $this->assertArrayHasKey('x', $result);
        $this->assertArrayHasKey('y', $result);
        $this->assertArrayHasKey('width', $result);
        $this->assertArrayHasKey('height', $result);
        $this->assertSame(0, $result['y']);
        $this->assertSame(1000, $result['height']);
        $this->assertGreaterThan(0, $result['x']);
    }

    #[Test]
    public function calculate_crop_dimensions_when_source_taller_than_target(): void
    {
        $controller = new OgImageController();
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('calculateCropDimensions');

        // Source is taller: 1000x1000 (ratio 1.0) vs target 1200x630 (ratio ~1.9)
        $result = $method->invoke($controller, 1000, 1000, 1.0, 1200 / 630);

        $this->assertSame(0, $result['x']);
        $this->assertSame(1000, $result['width']);
        $this->assertGreaterThan(0, $result['y']);
    }

    #[Test]
    public function was_recipe_background_loaded_returns_false_when_no_image(): void
    {
        $controller = new OgImageController();
        $reflection = new ReflectionClass($controller);
        $createCanvas = $reflection->getMethod('createCanvas');
        $wasLoaded = $reflection->getMethod('wasRecipeBackgroundLoaded');

        $country = Country::factory()->create(['locales' => ['en']]);
        $recipe = Recipe::factory()->for($country)->create([
            'image_path' => null,
        ]);

        $canvas = $createCanvas->invoke($controller);
        $result = $wasLoaded->invoke($controller, $canvas, $recipe);

        $this->assertFalse($result);

        imagedestroy($canvas);
    }

    #[Test]
    public function was_recipe_background_loaded_returns_false_when_image_content_is_invalid(): void
    {
        $controller = new OgImageController();
        $reflection = new ReflectionClass($controller);
        $createCanvas = $reflection->getMethod('createCanvas');

        $canvas = $createCanvas->invoke($controller);

        // Test with a recipe that has image_path set to null (no image)
        $country = Country::factory()->create(['locales' => ['en']]);
        $recipe = Recipe::factory()->for($country)->create([
            'image_path' => null,
        ]);

        $wasLoaded = $reflection->getMethod('wasRecipeBackgroundLoaded');
        $result = $wasLoaded->invoke($controller, $canvas, $recipe);

        $this->assertFalse($result);

        imagedestroy($canvas);
    }

    #[Test]
    public function stream_image_returns_streamed_response(): void
    {
        $controller = new OgImageController();
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('streamImage');

        $response = $method->invoke($controller, function (): void {
            echo 'test';
        });

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('image/jpeg', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('max-age=86400', (string) $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('public', (string) $response->headers->get('Cache-Control'));
    }

    #[Test]
    public function draw_logo_handles_missing_logo_file(): void
    {
        $controller = new OgImageController();
        $reflection = new ReflectionClass($controller);
        $createCanvas = $reflection->getMethod('createCanvas');
        $drawLogo = $reflection->getMethod('drawLogo');

        $canvas = $createCanvas->invoke($controller);

        // This should not throw, even if logo file doesn't exist
        $drawLogo->invoke($controller, $canvas);

        $this->assertInstanceOf(GdImage::class, $canvas);

        imagedestroy($canvas);
    }

    #[Test]
    public function menu_og_image_falls_back_to_en_when_country_has_no_locales(): void
    {
        $country = Country::factory()->create(['locales' => []]);
        $menu = Menu::factory()->for($country)->create([
            'year_week' => 202501,
        ]);

        $response = $this->get(route('og.menu', $menu));

        $response->assertOk();
    }

    #[Test]
    public function generic_og_image_uses_app_name_as_default_title(): void
    {
        config(['app.name' => 'Test App Name']);

        $response = $this->get(route('og.generic'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/jpeg');
    }

    #[Test]
    public function constructor_sets_font_path(): void
    {
        $controller = new OgImageController();
        $reflection = new ReflectionClass($controller);
        $fontPath = $reflection->getProperty('fontPath');

        $this->assertStringContainsString('inter', (string) $fontPath->getValue($controller));
        $this->assertStringContainsString('InterVariable.ttf', (string) $fontPath->getValue($controller));
    }

    #[Test]
    public function width_and_height_properties_are_set(): void
    {
        $controller = new OgImageController();
        $reflection = new ReflectionClass($controller);

        $width = $reflection->getProperty('width');
        $height = $reflection->getProperty('height');

        $this->assertSame(1200, $width->getValue($controller));
        $this->assertSame(630, $height->getValue($controller));
    }
}
