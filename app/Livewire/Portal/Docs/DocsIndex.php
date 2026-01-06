<?php

namespace App\Livewire\Portal\Docs;

use App\Livewire\AbstractComponent;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;

#[Layout('portal::components.layouts.app')]
class DocsIndex extends AbstractComponent
{
    public function render(): View
    {
        /** @var View $view */
        $view = view('portal::livewire.docs.index')->title('API Reference');

        return $view;
    }
}
