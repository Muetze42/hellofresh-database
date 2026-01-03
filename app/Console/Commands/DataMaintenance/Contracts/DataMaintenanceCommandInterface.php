<?php

declare(strict_types=1);

namespace App\Console\Commands\DataMaintenance\Contracts;

/**
 * Interface for data maintenance commands that can be run via the master cleanup command.
 *
 * Commands implementing this interface will be automatically discovered and executed
 * by the `data-maintenance:run-all` command.
 */
interface DataMaintenanceCommandInterface
{
    /**
     * Get the order in which this command should run.
     * Lower numbers run first.
     */
    public function getExecutionOrder(): int;
}
