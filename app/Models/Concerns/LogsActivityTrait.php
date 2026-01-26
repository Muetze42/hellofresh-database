<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @phpstan-require-extends Model
 */
trait LogsActivityTrait
{
    use LogsActivity;

    /**
     * @return list<string>
     */
    protected function getDontLogIfAttributesChangedOnly(): array
    {
        return ['created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * @return string[]
     */
    protected function logExceptAttributes(): array
    {
        return ['created_at', 'updated_at'];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->dontSubmitEmptyLogs()
            ->logAll()
            ->logExcept($this->logExceptAttributes())
            ->dontLogIfAttributesChangedOnly($this->getDontLogIfAttributesChangedOnly())
            ->logOnlyDirty()
            ->useLogName($this->getLogName());
    }

    protected function getLogName(): string
    {
        return $this->formatLogName($this::class);
    }

    protected function formatLogName(string $class): string
    {
        $className = Str::kebab(class_basename($class));

        return Str::plural(explode('-', $className)[0]);
    }

    /**
     * Get the event names that should be recorded.
     *
     * @return Collection<int, string>
     */
    protected static function eventsToBeRecorded(): Collection
    {
        $class = static::class;

        /** @var list<string> $doNotRecordEvents */
        $doNotRecordEvents = property_exists($class, 'doNotRecordEvents') ? $class::$doNotRecordEvents : [];
        $reject = collect($doNotRecordEvents);

        if (property_exists($class, 'recordEvents')) {
            /** @var list<string> $recordEvents */
            $recordEvents = $class::$recordEvents;

            return collect($recordEvents)->reject(fn (string $eventName): bool => $reject->contains($eventName));
        }

        $events = collect([
            'created',
            'updated',
            'deleted',
        ]);

        if (collect(class_uses_recursive(static::class))->contains(SoftDeletes::class)) {
            $events->push('restored');
        }

        return $events->reject(fn (string $eventName): bool => $reject->contains($eventName));
    }
}
