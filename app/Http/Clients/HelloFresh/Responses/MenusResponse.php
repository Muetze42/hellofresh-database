<?php

namespace App\Http\Clients\HelloFresh\Responses;

use Illuminate\Support\Facades\Date;

/**
 * @phpstan-type MenuRecipe array{
 *     id: string,
 *     name: string,
 *     slug: string,
 *     headline: string,
 *     difficulty: int,
 *     prepTime: string,
 *     imageLink: string|null,
 *     imagePath: string|null,
 *     country: string,
 *     websiteUrl: string,
 *     link: string
 * }
 * @phpstan-type MenuCourse array{
 *     recipe: MenuRecipe,
 *     bottle: mixed,
 *     index: int,
 *     quantity: int|null,
 *     isChosen: bool|null,
 *     isSoldOut: bool,
 *     isDefault: bool|null,
 *     isSwappable: bool|null,
 *     isRecommended: bool,
 *     presets: list<mixed>,
 *     premium: mixed,
 *     chargeSetting: mixed,
 *     selectionLimit: int|null
 * }
 * @phpstan-type Menu array{
 *     id: string,
 *     country: string,
 *     headline: string|null,
 *     description: string|null,
 *     product: string,
 *     week: string,
 *     cutoffDate: string|null,
 *     courses: list<MenuCourse>
 * }
 * @phpstan-type MenusData array{
 *     items: list<Menu>,
 *     take: int|null,
 *     skip: int|null,
 *     count: int,
 *     total: int|null
 * }
 *
 * @extends AbstractHelloFreshResponse<MenusData>
 */
class MenusResponse extends AbstractHelloFreshResponse
{
    /**
     * Get the JSON decoded body of the response as an array.
     *
     * @return MenusData
     */
    public function array(): array
    {
        return $this->toArray();
    }

    /**
     * Get the menu items.
     *
     * @return list<Menu>
     */
    public function items(): array
    {
        return $this->array()['items'];
    }

    /**
     * Get all menus with their week info and recipe IDs.
     * Only includes classic-box products, grouped by week.
     *
     * @return list<array{week: string, year_week: int, start: string, recipe_ids: list<string>}>
     */
    public function menus(): array
    {
        $menusByWeek = [];

        foreach ($this->items() as $item) {
            $week = $item['week'];

            $recipeIds = array_map(
                static fn (array $course): string => $course['recipe']['id'],
                $item['courses']
            );

            if (! isset($menusByWeek[$week])) {
                $menusByWeek[$week] = [
                    'week' => $week,
                    'year_week' => $this->parseYearWeek($week),
                    'start' => $this->getWeekStartDate($week),
                    'recipe_ids' => [],
                ];
            }

            $menusByWeek[$week]['recipe_ids'] = array_merge(
                $menusByWeek[$week]['recipe_ids'],
                $recipeIds
            );
        }

        return array_values($menusByWeek);
    }

    /**
     * Parse ISO week format (2025-W52) to integer (202552).
     */
    protected function parseYearWeek(string $week): int
    {
        // Format: 2025-W52 -> 202552
        $parts = explode('-W', $week);

        return (int) ($parts[0] . str_pad($parts[1], 2, '0', STR_PAD_LEFT));
    }

    /**
     * Get the start date (Saturday) of the week.
     */
    protected function getWeekStartDate(string $week): string
    {
        // HelloFresh weeks start on Saturday
        $date = Date::now();
        $date->setISODate((int) substr($week, 0, 4), (int) substr($week, 6));
        // ISO week starts Monday, HelloFresh starts Saturday (day before = -2)
        $date->modify('-2 days');

        return $date->format('Y-m-d');
    }
}
