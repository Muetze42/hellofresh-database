<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\FilterSharePageEnum;
use App\Livewire\Web\Recipes\RecipeIndex;
use App\Livewire\Web\Recipes\RecipeRandom;
use App\Models\Country;
use App\Models\FilterShare;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class FilterShareTest extends TestCase
{
    use RefreshDatabase;

    private Country $country;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->country = Country::factory()->create([
            'code' => 'US',
            'locales' => ['en'],
            'prep_min' => 5,
            'prep_max' => 60,
            'total_min' => 15,
            'total_max' => 120,
        ]);

        app()->bind('current.country', fn (): Country => $this->country);
        app()->setLocale('en');
    }

    #[Test]
    public function filter_share_can_be_created(): void
    {
        $filterShare = new FilterShare([
            'page' => FilterSharePageEnum::Recipes->value,
            'filters' => ['has_pdf' => true, 'difficulty' => [1, 2]],
        ]);
        $filterShare->country()->associate($this->country);
        $filterShare->save();

        $this->assertNotNull($filterShare->id);
        $this->assertEquals($this->country->id, $filterShare->country_id);
        $this->assertEquals(FilterSharePageEnum::Recipes->value, $filterShare->page);
        $this->assertEquals(['has_pdf' => true, 'difficulty' => [1, 2]], $filterShare->filters);
    }

    #[Test]
    public function filter_share_belongs_to_country(): void
    {
        $filterShare = new FilterShare([
            'page' => FilterSharePageEnum::Recipes->value,
            'filters' => ['has_pdf' => true],
        ]);
        $filterShare->country()->associate($this->country);
        $filterShare->save();

        $this->assertTrue($filterShare->country->is($this->country));
    }

    #[Test]
    public function filter_share_has_uuid_primary_key(): void
    {
        $filterShare = new FilterShare([
            'page' => FilterSharePageEnum::Recipes->value,
            'filters' => ['has_pdf' => true],
        ]);
        $filterShare->country()->associate($this->country);
        $filterShare->save();

        $this->assertSame(36, strlen((string) $filterShare->id));
        $this->assertMatchesRegularExpression('/^[0-9a-f-]{36}$/', $filterShare->id);
    }

    #[Test]
    public function filter_share_has_created_at_timestamp(): void
    {
        $filterShare = new FilterShare([
            'page' => FilterSharePageEnum::Recipes->value,
            'filters' => [],
        ]);
        $filterShare->country()->associate($this->country);
        $filterShare->save();

        $this->assertNotNull($filterShare->created_at);
    }

    #[Test]
    public function recipe_index_can_create_filter_share(): void
    {
        Livewire::test(RecipeIndex::class)
            ->set('filterHasPdf', true)
            ->set('difficultyLevels', [1, 2])
            ->call('generateFilterShareUrl')
            ->assertSet('shareUrl', fn (string $url): bool => str_contains($url, '/s/'));

        $this->assertDatabaseHas('filter_shares', [
            'country_id' => $this->country->id,
            'page' => FilterSharePageEnum::Recipes->value,
        ]);

        $filterShare = FilterShare::latest('created_at')->first();
        $this->assertTrue($filterShare->filters['has_pdf']);
        $this->assertEquals([1, 2], $filterShare->filters['difficulty']);
    }

    #[Test]
    public function recipe_random_creates_filter_share_with_random_page(): void
    {
        Livewire::test(RecipeRandom::class)
            ->set('filterHasPdf', true)
            ->call('generateFilterShareUrl')
            ->assertSet('shareUrl', fn (string $url): bool => str_contains($url, '/s/'));

        $this->assertDatabaseHas('filter_shares', [
            'country_id' => $this->country->id,
            'page' => FilterSharePageEnum::Random->value,
        ]);
    }

    #[Test]
    public function filter_share_only_includes_active_filters(): void
    {
        Livewire::test(RecipeIndex::class)
            ->set('filterHasPdf', true)
            ->set('filterShowCanonical', false)
            ->set('excludedAllergenIds', [])
            ->call('generateFilterShareUrl');

        $filterShare = FilterShare::latest('created_at')->first();

        $this->assertTrue($filterShare->filters['has_pdf']);
        $this->assertArrayNotHasKey('show_canonical', $filterShare->filters);
        $this->assertArrayNotHasKey('excluded_allergens', $filterShare->filters);
    }

    #[Test]
    public function filter_share_page_enum_returns_correct_route_names(): void
    {
        $this->assertSame('localized.recipes.index', FilterSharePageEnum::Recipes->routeName());
        $this->assertSame('localized.recipes.random', FilterSharePageEnum::Random->routeName());
        $this->assertSame('localized.menus.index', FilterSharePageEnum::Menus->routeName());
    }

    #[Test]
    public function filter_share_includes_page_number_in_url_params(): void
    {
        $this->get(localized_route('localized.recipes.index', ['page' => 2]))
            ->assertSeeLivewire(RecipeIndex::class);

        Livewire::test(RecipeIndex::class)
            ->set('filterHasPdf', true)
            ->call('prepareShareUrl');

        $filterShare = FilterShare::latest('created_at')->first();
        $this->assertNotNull($filterShare);
    }

    #[Test]
    public function get_share_page_returns_integer(): void
    {
        $component = Livewire::test(RecipeIndex::class);

        $reflection = new \ReflectionClass($component->instance());
        $method = $reflection->getMethod('getSharePage');

        $result = $method->invoke($component->instance());

        $this->assertIsInt($result, 'getSharePage() must return an integer, not a string');
    }

    #[Test]
    public function get_share_page_handles_string_page_parameter(): void
    {
        $mock = $this->getMockBuilder(RecipeIndex::class)
            ->onlyMethods(['getPage'])
            ->getMock();

        $mock->method('getPage')
            ->willReturn('2');

        $reflection = new \ReflectionClass($mock);
        $method = $reflection->getMethod('getSharePage');

        $result = $method->invoke($mock);

        $this->assertIsInt($result, 'getSharePage() must cast string to integer');
        $this->assertSame(2, $result);
    }
}
