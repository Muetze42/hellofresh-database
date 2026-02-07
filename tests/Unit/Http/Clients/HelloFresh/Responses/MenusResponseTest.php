<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Clients\HelloFresh\Responses;

use App\Http\Clients\HelloFresh\Responses\MenusResponse;
use GuzzleHttp\Psr7\Response as Psr7Response;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class MenusResponseTest extends TestCase
{
    #[Test]
    public function it_returns_array_data(): void
    {
        $response = $this->createResponse([
            'items' => [],
            'take' => 200,
            'skip' => 0,
            'count' => 0,
            'total' => 0,
        ]);

        $data = $response->array();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('items', $data);
    }

    #[Test]
    public function it_returns_items(): void
    {
        $response = $this->createResponse([
            'items' => [
                ['id' => 'menu-1', 'week' => '2025-W01'],
                ['id' => 'menu-2', 'week' => '2025-W02'],
            ],
            'take' => 200,
            'skip' => 0,
            'count' => 2,
            'total' => 2,
        ]);

        $items = $response->items();

        $this->assertCount(2, $items);
        $this->assertSame('menu-1', $items[0]['id']);
    }

    #[Test]
    public function it_returns_menus_grouped_by_week(): void
    {
        $response = $this->createResponse([
            'items' => [
                [
                    'id' => 'menu-1',
                    'product' => 'classic-box',
                    'week' => '2025-W01',
                    'courses' => [
                        ['recipe' => ['id' => 'recipe-1']],
                        ['recipe' => ['id' => 'recipe-2']],
                    ],
                ],
                [
                    'id' => 'menu-2',
                    'product' => 'classic-box',
                    'week' => '2025-W01',
                    'courses' => [
                        ['recipe' => ['id' => 'recipe-3']],
                    ],
                ],
            ],
            'take' => 200,
            'skip' => 0,
            'count' => 2,
            'total' => 2,
        ]);

        $menus = $response->menus();

        $this->assertCount(1, $menus);
        $this->assertSame('2025-W01', $menus[0]['week']);
        $this->assertSame(202501, $menus[0]['year_week']);
        $this->assertCount(3, $menus[0]['recipe_ids']);
        $this->assertContains('recipe-1', $menus[0]['recipe_ids']);
        $this->assertContains('recipe-2', $menus[0]['recipe_ids']);
        $this->assertContains('recipe-3', $menus[0]['recipe_ids']);
    }

    #[Test]
    public function it_groups_multiple_weeks(): void
    {
        $response = $this->createResponse([
            'items' => [
                [
                    'id' => 'menu-1',
                    'product' => 'classic-box',
                    'week' => '2025-W01',
                    'courses' => [
                        ['recipe' => ['id' => 'recipe-1']],
                    ],
                ],
                [
                    'id' => 'menu-2',
                    'product' => 'classic-box',
                    'week' => '2025-W02',
                    'courses' => [
                        ['recipe' => ['id' => 'recipe-2']],
                    ],
                ],
            ],
            'take' => 200,
            'skip' => 0,
            'count' => 2,
            'total' => 2,
        ]);

        $menus = $response->menus();

        $this->assertCount(2, $menus);
        $this->assertSame('2025-W01', $menus[0]['week']);
        $this->assertSame('2025-W02', $menus[1]['week']);
    }

    #[Test]
    public function it_parses_year_week_correctly(): void
    {
        $response = $this->createResponse([
            'items' => [
                [
                    'id' => 'menu-1',
                    'product' => 'classic-box',
                    'week' => '2025-W52',
                    'courses' => [
                        ['recipe' => ['id' => 'recipe-1']],
                    ],
                ],
            ],
            'take' => 200,
            'skip' => 0,
            'count' => 1,
            'total' => 1,
        ]);

        $menus = $response->menus();

        $this->assertSame(202552, $menus[0]['year_week']);
    }

    #[Test]
    public function it_pads_single_digit_week_number(): void
    {
        $response = $this->createResponse([
            'items' => [
                [
                    'id' => 'menu-1',
                    'product' => 'classic-box',
                    'week' => '2025-W5',
                    'courses' => [
                        ['recipe' => ['id' => 'recipe-1']],
                    ],
                ],
            ],
            'take' => 200,
            'skip' => 0,
            'count' => 1,
            'total' => 1,
        ]);

        $menus = $response->menus();

        $this->assertSame(202505, $menus[0]['year_week']);
    }

    #[Test]
    public function it_calculates_week_start_date(): void
    {
        $response = $this->createResponse([
            'items' => [
                [
                    'id' => 'menu-1',
                    'product' => 'classic-box',
                    'week' => '2025-W01',
                    'courses' => [
                        ['recipe' => ['id' => 'recipe-1']],
                    ],
                ],
            ],
            'take' => 200,
            'skip' => 0,
            'count' => 1,
            'total' => 1,
        ]);

        $menus = $response->menus();

        $this->assertArrayHasKey('start', $menus[0]);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $menus[0]['start']);
        // Week start date should be in the year 2024 or 2025 (ISO week 1 of 2025 starts in late Dec 2024)
        $this->assertStringStartsWith('202', $menus[0]['start']);
    }

    #[Test]
    public function it_parses_four_digit_year_correctly(): void
    {
        $response = $this->createResponse([
            'items' => [
                [
                    'id' => 'menu-1',
                    'product' => 'classic-box',
                    'week' => '2025-W10',
                    'courses' => [
                        ['recipe' => ['id' => 'recipe-1']],
                    ],
                ],
            ],
            'take' => 200,
            'skip' => 0,
            'count' => 1,
            'total' => 1,
        ]);

        $menus = $response->menus();

        // Year week should be 202510, not some malformed value
        $this->assertSame(202510, $menus[0]['year_week']);
        // Start date should be in 2025, not some other year
        $this->assertStringStartsWith('2025-', $menus[0]['start']);
    }

    #[Test]
    public function it_handles_different_years(): void
    {
        $response = $this->createResponse([
            'items' => [
                [
                    'id' => 'menu-1',
                    'product' => 'classic-box',
                    'week' => '2024-W52',
                    'courses' => [
                        ['recipe' => ['id' => 'recipe-1']],
                    ],
                ],
            ],
            'take' => 200,
            'skip' => 0,
            'count' => 1,
            'total' => 1,
        ]);

        $menus = $response->menus();

        $this->assertSame(202452, $menus[0]['year_week']);
        $this->assertStringStartsWith('2024-', $menus[0]['start']);
    }

    #[Test]
    public function it_returns_empty_menus_for_empty_items(): void
    {
        $response = $this->createResponse([
            'items' => [],
            'take' => 200,
            'skip' => 0,
            'count' => 0,
            'total' => 0,
        ]);

        $menus = $response->menus();

        $this->assertSame([], $menus);
    }

    protected function createResponse(array $data): MenusResponse
    {
        $psr7Response = new Psr7Response(200, [], json_encode($data));

        return new MenusResponse($psr7Response);
    }
}
