<?php

namespace App\Livewire\Portal\Stats;

use App\Livewire\AbstractComponent;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;

#[Layout('portal::components.layouts.app')]
class StatsIndex extends AbstractComponent
{
    public function render(): View
    {
        /** @var View $view */
        $view = view('portal::livewire.stats.index')->title(page_title('Statistics'));

        return $view;
    }
}
