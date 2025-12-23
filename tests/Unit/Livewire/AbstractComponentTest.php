<?php

declare(strict_types=1);

namespace Tests\Unit\Livewire;

use App\Livewire\AbstractComponent;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AbstractComponentTest extends TestCase
{
    #[Test]
    public function it_trims_string_values(): void
    {
        $component = Livewire::test(TestAbstractComponent::class);
        $instance = $component->instance();

        $result = $instance->publicTrimStringsTransform(['name' => '  Test  ', 'email' => '  test@example.com  ']);

        $this->assertSame('Test', $result['name']);
        $this->assertSame('test@example.com', $result['email']);
    }

    #[Test]
    public function it_converts_empty_strings_to_null(): void
    {
        $component = Livewire::test(TestAbstractComponent::class);
        $instance = $component->instance();

        $result = $instance->publicConvertEmptyStringsToNullTransform(['name' => '', 'email' => 'test@example.com']);

        $this->assertNull($result['name']);
        $this->assertSame('test@example.com', $result['email']);
    }

    #[Test]
    public function it_excludes_password_fields_from_trimming(): void
    {
        $component = Livewire::test(TestAbstractComponent::class);
        $instance = $component->instance();

        $result = $instance->publicTrimStringsTransform([
            'password' => '  secret123  ',
            'current_password' => '  oldpass  ',
            'password_confirmation' => '  secret123  ',
        ]);

        $this->assertSame('  secret123  ', $result['password']);
        $this->assertSame('  oldpass  ', $result['current_password']);
        $this->assertSame('  secret123  ', $result['password_confirmation']);
    }

    #[Test]
    public function it_handles_nested_arrays(): void
    {
        $component = Livewire::test(TestAbstractComponent::class);
        $instance = $component->instance();

        $result = $instance->publicTrimStringsTransform([
            'items' => ['  first  ', '  second  '],
        ]);

        $this->assertSame('first', $result['items'][0]);
        $this->assertSame('second', $result['items'][1]);
    }

    #[Test]
    public function it_skips_non_string_values(): void
    {
        $component = Livewire::test(TestAbstractComponent::class);
        $instance = $component->instance();

        $result = $instance->publicTrimStringsTransform([
            'count' => 42,
            'active' => true,
            'data' => null,
        ]);

        $this->assertSame(42, $result['count']);
        $this->assertTrue($result['active']);
        $this->assertNull($result['data']);
    }

    #[Test]
    public function realtime_mapping_trims_string_values(): void
    {
        $component = Livewire::test(TestAbstractComponent::class);
        $instance = $component->instance();

        $instance->name = 'original';
        $instance->publicRealtimeMapping('name', '  trimmed  ');

        $this->assertSame('trimmed', $instance->name);
    }

    #[Test]
    public function realtime_mapping_converts_empty_to_null(): void
    {
        $component = Livewire::test(TestAbstractComponent::class);
        $instance = $component->instance();

        $instance->name = 'original';
        $instance->publicRealtimeMapping('name', '');

        $this->assertNull($instance->name);
    }

    #[Test]
    public function realtime_mapping_converts_rm_marker_to_null(): void
    {
        $component = Livewire::test(TestAbstractComponent::class);
        $instance = $component->instance();

        $instance->name = 'original';
        $instance->publicRealtimeMapping('name', '__rm__');

        $this->assertNull($instance->name);
    }

    #[Test]
    public function realtime_mapping_converts_null_string_to_null(): void
    {
        $component = Livewire::test(TestAbstractComponent::class);
        $instance = $component->instance();

        $instance->name = 'original';
        $instance->publicRealtimeMapping('name', 'null');

        $this->assertNull($instance->name);
    }

    #[Test]
    public function realtime_mapping_skips_excluded_properties(): void
    {
        $component = Livewire::test(TestAbstractComponent::class);
        $instance = $component->instance();

        $instance->password = '  secret  ';
        $instance->publicRealtimeMapping('password', '  updated  ');

        $this->assertSame('  secret  ', $instance->password);
    }

    #[Test]
    public function realtime_mapping_skips_non_string_values(): void
    {
        $component = Livewire::test(TestAbstractComponent::class);
        $instance = $component->instance();

        $instance->name = 'original';
        $instance->publicRealtimeMapping('name', 42);

        $this->assertSame('original', $instance->name);
    }

    #[Test]
    public function realtime_mapping_skips_nonexistent_properties(): void
    {
        $component = Livewire::test(TestAbstractComponent::class);
        $instance = $component->instance();

        $instance->publicRealtimeMapping('nonexistent', 'value');

        $this->assertFalse(property_exists($instance, 'nonexistent'));
    }

    #[Test]
    public function realtime_mapping_handles_nested_array_properties(): void
    {
        $component = Livewire::test(TestAbstractComponent::class);
        $instance = $component->instance();

        $instance->data = ['nested' => ['value' => 'original']];
        $instance->publicRealtimeMapping('data.nested.value', '  updated  ');

        $this->assertSame('updated', $instance->data['nested']['value']);
    }

    #[Test]
    public function realtime_mapping_filters_empty_nested_values(): void
    {
        $component = Livewire::test(TestAbstractComponent::class);
        $instance = $component->instance();

        $instance->data = ['items' => ['first', 'second']];
        $instance->publicRealtimeMapping('data.items.0', '');

        // The array_filter in realtimeMapping removes null values, so the array is reindexed
        $this->assertArrayNotHasKey(0, $instance->data['items']);
    }

    #[Test]
    public function except_from_realtime_mapping_returns_trim_exceptions(): void
    {
        $component = Livewire::test(TestAbstractComponent::class);
        $instance = $component->instance();

        $exceptions = $instance->publicExceptFromRealtimeMappingProperties();

        $this->assertContains('password', $exceptions);
        $this->assertContains('current_password', $exceptions);
        $this->assertContains('password_confirmation', $exceptions);
    }
}

class TestAbstractComponent extends AbstractComponent
{
    public ?string $name = '';

    public string $password = '';

    public array $data = [];

    public function publicTrimStringsTransform(array $attributes): array
    {
        return $this->trimStringsTransform($attributes);
    }

    public function publicConvertEmptyStringsToNullTransform(array $attributes): array
    {
        return $this->convertEmptyStringsToNullTransform($attributes);
    }

    public function publicRealtimeMapping(string $property, mixed $value): void
    {
        $this->realtimeMapping($property, $value);
    }

    public function publicExceptFromRealtimeMappingProperties(): array
    {
        return $this->exceptFromRealtimeMappingProperties();
    }

    public function render(): string
    {
        return '<div>Test Component</div>';
    }
}
