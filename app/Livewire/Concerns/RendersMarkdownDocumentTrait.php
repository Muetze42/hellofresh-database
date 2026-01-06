<?php

namespace App\Livewire\Concerns;

use App\Support\Markdown\FluxRenderer;
use Illuminate\Support\Facades\File;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Parser\MarkdownParser;

trait RendersMarkdownDocumentTrait
{
    /**
     * Render a markdown file to HTML using FluxRenderer.
     */
    protected function renderMarkdownFile(string $path): ?string
    {
        if (! File::exists($path)) {
            return null;
        }

        $markdown = File::get($path);

        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());

        $parser = new MarkdownParser($environment);
        $document = $parser->parse($markdown);

        return new FluxRenderer()->render($document);
    }

    /**
     * Get the path to a localized document with fallback to English.
     *
     * @param  string  $basePath  Base path pattern with %s placeholder for locale (e.g., 'docs/privacy/%s.md')
     * @param  string  $locale  Current locale
     */
    protected function getLocalizedDocumentPath(string $basePath, string $locale): string
    {
        $path = resource_path(sprintf($basePath, $locale));

        if (File::exists($path)) {
            return $path;
        }

        return resource_path(sprintf($basePath, 'en'));
    }

    /**
     * Render a localized markdown document with fallback to English.
     *
     * @param  string  $basePath  Base path pattern with %s placeholder for locale
     * @param  string  $locale  Current locale
     */
    protected function renderLocalizedDocument(string $basePath, string $locale): ?string
    {
        $path = $this->getLocalizedDocumentPath($basePath, $locale);

        return $this->renderMarkdownFile($path);
    }
}
