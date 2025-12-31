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
        $version = config('api.version');

        return view('portal::livewire.docs.get-started', [
            'baseUrl' => 'https://' . config('api.domain_name'),
            'rateLimit' => config('api.rate_limit'),
            'paginationDefault' => config('api.pagination.per_page_default'),
            'paginationMin' => config('api.pagination.per_page_min'),
            'paginationMax' => config('api.pagination.per_page_max'),
            'version' => $version,
            'isPreRelease' => version_compare($version, '1.0.0', '<'),
        ])->title('Get Started');
    }
}
