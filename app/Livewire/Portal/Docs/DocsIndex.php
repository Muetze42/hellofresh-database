<?php

namespace App\Livewire\Portal\Docs;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('portal::components.layouts.app')]
class DocsIndex extends Component
{
    public function render(): View
    {
        /** @var View $view */
        $view = view('portal::livewire.docs.index')->title('API Reference');

        return $view;
    }
}
