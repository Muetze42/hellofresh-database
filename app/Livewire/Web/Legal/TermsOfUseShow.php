<?php

namespace App\Livewire\Web\Legal;

use App\Livewire\AbstractComponent;
use App\Livewire\Concerns\RendersMarkdownDocumentTrait;
use App\Livewire\Web\Concerns\WithLocalizedContextTrait;
use Illuminate\Contracts\View\View as ViewInterface;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

#[Layout('web::components.layouts.localized')]
class TermsOfUseShow extends AbstractComponent
{
    use RendersMarkdownDocumentTrait;
    use WithLocalizedContextTrait;

    /**
     * Get the terms of use content as HTML.
     */
    #[Computed]
    public function content(): ?string
    {
        return $this->renderLocalizedDocument('docs/terms/%s.md', $this->locale);
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): ViewInterface
    {
        return view('web::livewire.terms-of-use.terms-of-use-show');
    }
}
