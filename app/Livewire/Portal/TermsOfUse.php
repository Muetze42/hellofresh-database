<?php

namespace App\Livewire\Portal;

use App\Support\Markdown\FluxRenderer;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Parser\MarkdownParser;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('portal::components.layouts.app')]
class TermsOfUse extends Component
{
    /**
     * Get the rendered terms of use content.
     */
    #[Computed]
    public function content(): string
    {
        $path = resource_path('docs/terms/en.md');

        if (! File::exists($path)) {
            return '';
        }

        $markdown = File::get($path);

        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());

        $parser = new MarkdownParser($environment);
        $document = $parser->parse($markdown);

        return new FluxRenderer()->render($document);
    }

    public function render(): View
    {
        return view('portal::livewire.terms-of-use')->title('Terms of Use');
    }
}
