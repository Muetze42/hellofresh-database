<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\FilterSharePageEnum;
use App\Models\Country;
use App\Models\FilterShare;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FilterShareController extends Controller
{
    /**
     * Apply filters to session and redirect to the target page.
     */
    public function __invoke(Request $request, string $id): RedirectResponse
    {
        /** @var Country $country */
        $country = resolve('current.country');

        $filterShare = FilterShare::find($id);

        abort_if($filterShare === null || $filterShare->country_id !== $country->id, 404);

        $page = FilterSharePageEnum::tryFrom($filterShare->page);

        abort_if($page === null, 404);

        $this->applyFiltersToSession($filterShare, $country);

        // Pass through URL params (search, page, sort)
        $queryParams = [];

        if ($request->filled('search')) {
            $queryParams['search'] = $request->query('search');
        }

        if ($request->filled('page') && (int) $request->query('page') > 1) {
            $queryParams['page'] = (int) $request->query('page');
        }

        if ($request->filled('sort')) {
            $queryParams['sort'] = $request->query('sort');
        }

        $redirectUrl = localized_route($page->routeName());

        if ($queryParams !== []) {
            $redirectUrl .= '?' . http_build_query($queryParams);
        }

        return redirect($redirectUrl);
    }

    /**
     * Apply the filter share's filters to the session.
     */
    protected function applyFiltersToSession(FilterShare $filterShare, Country $country): void
    {
        $filters = $filterShare->filters;
        $prefix = sprintf('recipe_filter_%d_', $country->id);

        $mapping = [
            'has_pdf' => 'has_pdf',
            'show_canonical' => 'show_canonical',
            'excluded_allergens' => 'excluded_allergens',
            'ingredients' => 'ingredients',
            'ingredient_match' => 'ingredient_match',
            'excluded_ingredients' => 'excluded_ingredients',
            'tags' => 'tags',
            'excluded_tags' => 'excluded_tags',
            'labels' => 'labels',
            'excluded_labels' => 'excluded_labels',
            'difficulty' => 'difficulty',
            'prep_time' => 'prep_time',
            'total_time' => 'total_time',
        ];

        foreach ($mapping as $filterKey => $sessionKey) {
            if (array_key_exists($filterKey, $filters)) {
                session()->put($prefix . $sessionKey, $filters[$filterKey]);
            }
        }
    }
}
