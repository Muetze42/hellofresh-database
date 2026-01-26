<?php

namespace App\Models\Concerns;

trait LogsModificationsTrait
{
    use LogsActivityTrait;

    /**
     * Events to not record for activity logging.
     *
     * @var list<string>
     */
    protected static array $doNotRecordEvents = [
        'created',
    ];
}
