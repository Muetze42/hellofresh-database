<?php

namespace App\Livewire\Portal\Stats;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('portal::components.layouts.app')]
class StatsIndex extends Component
{
    public function render(): View
    {
        /** @var View $view */
        $view = view('portal::livewire.stats.index')->title(page_title('Statistics'));

        return $view;
    }
}
