<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\RedirectResponse;

class MenuRedirectController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): RedirectResponse
    {
        $today = today();
        $countryId = current_country()->id;

        $menu = Menu::where('country_id', $countryId)
            ->where('start', '<=', $today)
            ->orderByDesc('start')
            ->first()
            ?? Menu::where('country_id', $countryId)
                ->orderBy('start')
                ->first();

        abort_if($menu === null, 404);

        return redirect()->to(localized_route('localized.menus.show', ['menu' => $menu->year_week]));
    }
}
