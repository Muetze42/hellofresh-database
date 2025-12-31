<?php

namespace App\Livewire\Web;

use App\Models\Country;
use Illuminate\Contracts\View\View as ViewInterface;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

#[Layout('web::components.layouts.app')]
class RegionSelect extends AbstractComponent
{
    /**
     * Convert a country code to a flag emoji.
     */
    public function getFlagEmoji(string $countryCode): string
    {
        $codePoints = array_map(
            static fn (string $char): string => mb_chr(ord($char) - ord('A') + 0x1F1E6),
            str_split(strtoupper($countryCode))
        );

        return implode('', $codePoints);
    }

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
