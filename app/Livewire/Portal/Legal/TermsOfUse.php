<?php

namespace App\Livewire\Portal\Legal;

use App\Livewire\Concerns\RendersMarkdownDocumentTrait;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('portal::components.layouts.app')]
class TermsOfUse extends Component
{
    use RendersMarkdownDocumentTrait;

    /**
     * Get the rendered terms of use content.
     */
    #[Computed]
    public function content(): ?string
    {
        return $this->renderMarkdownFile(resource_path('docs/terms/en.md'));
    }

    public function render(): View
    {
        return view('portal::livewire.terms-of-use')->title('Terms of Use');
    }
}
