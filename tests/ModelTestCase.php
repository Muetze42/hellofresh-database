<?php

// phpcs:ignoreFile

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class ModelTestCase extends TestCase
{
    protected Model $instance;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $class = class_basename(static::class);
        $this->instance = app('App\Models\\' . substr($class, 0, -4));
    }

    protected function resource(): Model
    {
        return $this->instance->factory()->create();
    }

    public function test_can_create(): void
    {
        $resource = $this->resource();

        $this->assertModelExists($resource);
        $this->assertDatabaseHas($this->instance->getTable(), [
            $this->instance->getKeyName() => $resource->getKey(),
        ]);
    }

    public function test_can_delete(): void
    {
        $resource = $this->resource();
        $resource->delete();

        if (in_array(SoftDeletes::class, class_uses($resource))) {
            $this->assertSoftDeleted($resource);
            $resource->forceDelete();
        }

        $this->assertModelMissing($resource);
    }

    public function test_can_restore(): void
    {
        $resource = $this->resource();
        $resource->delete();

        if (in_array(SoftDeletes::class, class_uses($resource))) {
            $resource->restore();
            $this->assertNotSoftDeleted($resource);
        } else {
            $this->assertFalse(false);
        }
    }
}
