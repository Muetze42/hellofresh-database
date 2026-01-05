<?php

namespace App\Livewire\Web;

use App\Livewire\AbstractComponent;
use App\Models\Country;
use Illuminate\Contracts\View\View as ViewInterface;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

#[Layout('web::components.layouts.app')]
class RegionSelect extends AbstractComponent
{
    /**
     * Get all active countries.
     *
     * @return Collection<int, Country>
     */
    #[Computed]
    public function countries(): Collection
    {
        return Country::active()->orderBy('code')->get();
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): ViewInterface
    {
        return view('web::livewire.region-select')
            ->title(config('app.name'));
    }
}
