<?php

namespace App\Livewire\Portal\Legal;

use App\Livewire\Concerns\RendersMarkdownDocumentTrait;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('portal::components.layouts.app')]
class PrivacyPolicy extends Component
{
    use RendersMarkdownDocumentTrait;

    /**
     * Get the rendered privacy policy content.
     */
    #[Computed]
    public function content(): ?string
    {
        return $this->renderMarkdownFile(resource_path('docs/privacy/en.md'));
    }

    public function render(): View
    {
        return view('portal::livewire.privacy-policy')->title('Privacy Policy');
    }
}
