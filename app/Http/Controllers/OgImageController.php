<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Recipe;
use GdImage;
use GDText\Box;
use GDText\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OgImageController extends Controller
{
    /**
     * @var int<1, max>
     */
    protected int $width = 1200;

    /**
     * @var int<1, max>
     */
    protected int $height = 630;

    protected string $fontPath;

    public function __construct()
    {
        $this->fontPath = resource_path('fonts/inter/InterVariable.ttf');
    }

    /**
     * Generate OG image for a recipe.
     */
    public function recipe(Recipe $recipe): StreamedResponse
    {
        return $this->streamImage(function () use ($recipe): void {
            $canvas = $this->createCanvas();

            if (! $this->wasRecipeBackgroundLoaded($canvas, $recipe)) {
                $this->fillSolidBackground($canvas);
            }

            $this->addGradientOverlay($canvas);
            $this->drawTitle($canvas, $recipe->name);
            $this->drawBadge($canvas);

            imagejpeg($canvas, null, 90);
            imagedestroy($canvas);
        });
    }

    /**
     * Generate OG image for a menu.
     */
    public function menu(Request $request, Menu $menu): StreamedResponse
    {
        $locale = $request->string('locale')->toString();

        if ($locale === '') {
            $menu->loadMissing('country');
            $locale = $menu->country?->locales[0] ?? 'en';
        }

        app()->setLocale($locale);

        return $this->streamImage(function () use ($menu): void {
            $canvas = $this->createCanvas();

            $this->fillSolidBackground($canvas);

            $year = intdiv($menu->year_week, 100);
            $week = $menu->year_week % 100;

            $title = sprintf(__('Menu Week %d/%d'), $week, $year);
            $this->drawTitle($canvas, $title, withLogo: true);

            $this->drawBadge($canvas);

            imagejpeg($canvas, null, 90);
            imagedestroy($canvas);
        });
    }

    /**
     * Generate generic OG image with a title.
     */
    public function generic(Request $request): StreamedResponse
    {
        $title = $request->string('title', config('app.name'))->toString();
        $font = $request->string('font')->toString();

        if ($font === 'mono') {
            $this->fontPath = resource_path('fonts/jet-brains-mono/ttf/JetBrainsMono-Medium.ttf');
        }

        return $this->streamImage(function () use ($title): void {
            $canvas = $this->createCanvas();

            $this->fillSolidBackground($canvas);

            $this->drawTitle($canvas, $title, withLogo: true);

            $this->drawBadge($canvas);

            imagejpeg($canvas, null, 90);
            imagedestroy($canvas);
        });
    }

    /**
     * Stream an image response.
     */
    protected function streamImage(callable $callback): StreamedResponse
    {
        return response()->stream($callback, 200, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * Create a blank canvas with the standard OG dimensions.
     */
    protected function createCanvas(): GdImage
    {
        $canvas = imagecreatetruecolor($this->width, $this->height);
        imagealphablending($canvas, true);

        return $canvas;
    }

    /**
     * Fill canvas with a gradient background.
     */
    protected function fillSolidBackground(GdImage $canvas): void
    {
        for ($y = 0; $y < $this->height; $y++) {
            $progress = $y / $this->height;

            /** @var int<0, 255> $red */
            $red = (int) (24 + ($progress * (39 - 24)));
            /** @var int<0, 255> $green */
            $green = (int) (24 + ($progress * (39 - 24)));
            /** @var int<0, 255> $blue */
            $blue = (int) (27 + ($progress * (42 - 27)));

            $color = imagecolorallocate($canvas, $red, $green, $blue);

            if ($color !== false) {
                imageline($canvas, 0, $y, $this->width, $y, $color);
            }
        }

        $accentColor = imagecolorallocate($canvas, 145, 193, 30);
        if ($accentColor !== false) {
            imagefilledrectangle($canvas, 0, $this->height - 4, $this->width, $this->height, $accentColor);
        }
    }

    /**
     * Load recipe image as background and return whether it was successful.
     */
    protected function wasRecipeBackgroundLoaded(GdImage $canvas, Recipe $recipe): bool
    {
        $imageUrl = $recipe->header_image_url;

        if (! $imageUrl) {
            return false;
        }

        $response = Http::timeout(10)->get($imageUrl);

        if ($response->failed()) {
            return false;
        }

        $imageContent = $response->body();

        if ($imageContent === '') {
            return false;
        }

        $sourceImage = imagecreatefromstring($imageContent);

        if ($sourceImage === false) {
            return false;
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        $sourceRatio = $sourceWidth / $sourceHeight;
        $targetRatio = $this->width / $this->height;

        $cropDimensions = $this->calculateCropDimensions(
            $sourceWidth,
            $sourceHeight,
            $sourceRatio,
            $targetRatio
        );

        imagecopyresampled(
            $canvas,
            $sourceImage,
            0,
            0,
            $cropDimensions['x'],
            $cropDimensions['y'],
            $this->width,
            $this->height,
            $cropDimensions['width'],
            $cropDimensions['height']
        );

        imagedestroy($sourceImage);

        return true;
    }

    /**
     * Calculate crop dimensions for image resizing.
     *
     * @return array{x: int, y: int, width: int, height: int}
     */
    protected function calculateCropDimensions(
        int $sourceWidth,
        int $sourceHeight,
        float $sourceRatio,
        float $targetRatio
    ): array {
        if ($sourceRatio > $targetRatio) {
            $cropHeight = $sourceHeight;
            $cropWidth = (int) ($sourceHeight * $targetRatio);

            return [
                'x' => (int) (($sourceWidth - $cropWidth) / 2),
                'y' => 0,
                'width' => $cropWidth,
                'height' => $cropHeight,
            ];
        }

        $cropWidth = $sourceWidth;
        $cropHeight = (int) ($sourceWidth / $targetRatio);

        return [
            'x' => 0,
            'y' => (int) (($sourceHeight - $cropHeight) / 2),
            'width' => $cropWidth,
            'height' => $cropHeight,
        ];
    }

    /**
     * Add a gradient overlay for better text readability.
     */
    protected function addGradientOverlay(GdImage $canvas): void
    {
        $gradientStart = $this->height * 0.3;

        for ($y = 0; $y < $this->height; $y++) {
            if ($y < $gradientStart) {
                continue;
            }

            $progress = ($y - $gradientStart) / ($this->height - $gradientStart);

            /** @var int<0, 127> $alpha */
            $alpha = max(0, min(127, (int) (127 - ($progress * 100))));

            $color = imagecolorallocatealpha($canvas, 0, 0, 0, $alpha);

            if ($color !== false) {
                imageline($canvas, 0, $y, $this->width, $y, $color);
            }
        }
    }

    /**
     * Draw the main title text.
     */
    protected function drawTitle(GdImage $canvas, string $title, bool $withLogo = false): void
    {
        if ($withLogo) {
            $this->drawLogo($canvas);
        }

        $box = new Box($canvas);
        $box->setFontFace($this->fontPath);
        $box->setFontColor(new Color(255, 255, 255));
        $box->setFontSize(56);

        $textY = $withLogo ? $this->height - 150 : $this->height - 200;
        $textHeight = $withLogo ? 100 : 140;

        $box->setBox(60, $textY, $this->width - 120, $textHeight);
        $box->setTextAlign('center', 'center');
        $box->draw($title);
    }

    /**
     * Draw the logo on the canvas.
     */
    protected function drawLogo(GdImage $canvas): void
    {
        $logoPath = public_path('assets/favicons/production.png');

        if (! file_exists($logoPath)) {
            return;
        }

        $logo = imagecreatefrompng($logoPath);

        if ($logo === false) {
            return;
        }

        $logoSize = 200;
        $logoX = (int) (($this->width - $logoSize) / 2);
        $logoY = 120;

        imagecopyresampled(
            $canvas,
            $logo,
            $logoX,
            $logoY,
            0,
            0,
            $logoSize,
            $logoSize,
            imagesx($logo),
            imagesy($logo)
        );

        imagedestroy($logo);
    }

    /**
     * Draw the hfresh.info badge.
     */
    protected function drawBadge(GdImage $canvas): void
    {
        $box = new Box($canvas);
        $box->setFontFace($this->fontPath);
        $box->setFontColor(new Color(255, 255, 255, 40));
        $box->setFontSize(20);
        $box->setBox($this->width - 160, $this->height - 50, 140, 30);
        $box->setTextAlign('right', 'bottom');
        $box->draw('hfresh.info');
    }
}
