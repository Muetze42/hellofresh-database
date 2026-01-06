<?php

namespace App\Livewire\Web\Legal;

use App\Livewire\AbstractComponent;
use App\Livewire\Web\Concerns\WithLocalizedContextTrait;
use App\Support\Markdown\FluxRenderer;
use Illuminate\Contracts\View\View as ViewInterface;
use Illuminate\Support\Facades\File;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Parser\MarkdownParser;
use Livewire\Attributes\Computed;

class PrivacyPolicyShow extends AbstractComponent
{
    use WithLocalizedContextTrait;

    /**
     * Get the privacy policy content as HTML.
     */
    #[Computed]
    public function content(): ?string
    {
        $markdown = $this->getMarkdownContent();

        if ($markdown === null) {
            return null;
        }

        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());

        $parser = new MarkdownParser($environment);
        $document = $parser->parse($markdown);

        return new FluxRenderer()->render($document);
    }

    /**
     * Get the markdown content for the current locale with fallback to English.
     */
    protected function getMarkdownContent(): ?string
    {
        $path = $this->getPrivacyPolicyPath($this->locale);

        if (File::exists($path)) {
            return File::get($path);
        }

        $fallbackPath = $this->getPrivacyPolicyPath('en');

        if (File::exists($fallbackPath)) {
            return File::get($fallbackPath);
        }

        return null;
    }

    /**
     * Get the path to the privacy policy markdown file.
     */
    protected function getPrivacyPolicyPath(string $locale): string
    {
        return resource_path(sprintf('docs/privacy/%s.md', $locale));
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): ViewInterface
    {
        return view('web::livewire.privacy-policy.privacy-policy-show');
    }
}
