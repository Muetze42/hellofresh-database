<?php

namespace App\Livewire\Portal\Resources;

use App\Livewire\AbstractComponent;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;

#[Layout('portal::components.layouts.app')]
class ResourcesIndex extends AbstractComponent
{
    public function render(): View
    {
        /** @var View $view */
        $view = view('portal::livewire.resources.index')->title(page_title('Resources'));

        return $view;
    }
}
