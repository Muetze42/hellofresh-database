<?php

namespace App\Jobs;

use App\Enums\QueueEnum;
use App\Models\Country;
use App\Models\Recipe;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @method static void dispatch(Country $country, string $locale)
 */
class GenerateSitemapJob implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Country $country,
        public string $locale,
    ) {
        $this->onQueue(QueueEnum::Long->value);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Set locale for proper slug generation
        app()->setLocale($this->locale);

        $xml = $this->buildSitemapXml();
        $filename = $this->getSitemapFilename();

        Storage::disk('public')->put('sitemaps/' . $filename, $xml);
    }

    /**
     * Build the sitemap XML content.
     */
    protected function buildSitemapXml(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Add homepage
        $xml .= $this->buildUrlEntry(
            localized_route('localized.recipes.index', [], true, $this->country, $this->locale),
            now()->toDateString(),
            'daily',
            '1.0'
        );

        // Add recipes in chunks to avoid memory issues
        Recipe::where('country_id', $this->country->id)
            ->select(['id', 'hellofresh_id', 'name', 'updated_at'])
            ->orderBy('id')
            ->chunk(1000, function (Collection $recipes) use (&$xml): void {
                foreach ($recipes as $recipe) {
                    $name = $recipe->getTranslation('name', $this->locale, useFallbackLocale: false)
                        ?: $recipe->getFirstTranslation('name');

                    if ($name === null) {
                        continue;
                    }

                    $slug = Str::slug($name, language: $this->locale);
                    $url = localized_route(
                        'localized.recipes.show',
                        ['slug' => $slug, 'recipe' => $recipe->hellofresh_id],
                        true,
                        $this->country,
                        $this->locale
                    );

                    $xml .= $this->buildUrlEntry(
                        $url,
                        $recipe->updated_at?->toDateString() ?? now()->toDateString(),
                        'monthly',
                        '0.8'
                    );
                }
            });

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Build a single URL entry for the sitemap.
     */
    protected function buildUrlEntry(string $loc, string $lastmod, string $changefreq, string $priority): string
    {
        return sprintf(
            "  <url>\n    <loc>%s</loc>\n    <lastmod>%s</lastmod>\n    <changefreq>%s</changefreq>\n    <priority>%s</priority>\n  </url>\n",
            htmlspecialchars($loc, ENT_XML1, 'UTF-8'),
            $lastmod,
            $changefreq,
            $priority
        );
    }

    /**
     * Get the sitemap filename for this country/locale combination.
     */
    protected function getSitemapFilename(): string
    {
        return sprintf('sitemap-%s-%s.xml', $this->locale, $this->country->code);
    }
}
