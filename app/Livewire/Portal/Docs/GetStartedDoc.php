<?php

namespace App\Livewire\Portal\Docs;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('portal::components.layouts.app')]
class GetStartedDoc extends Component
{
    public function render(): View
    {
        return view('portal::livewire.docs.get-started', [
            'baseUrl' => 'https://' . config('api.domain_name'),
            'rateLimit' => config('api.rate_limit'),
            'paginationDefault' => config('api.pagination.per_page_default'),
            'paginationMin' => config('api.pagination.per_page_min'),
            'paginationMax' => config('api.pagination.per_page_max'),
        ])->title('Get Started');
    }
}
