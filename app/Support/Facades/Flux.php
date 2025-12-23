<?php

namespace App\Support\Facades;

use App\Services\FluxService;
use Flux\FluxManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static FluxManager modal(string $name)
 * @method static void toast(string $text, ?string $heading = null, ?string $variant = null)
 * @method static void toastSuccess(string $text, ?string $heading = null)
 * @method static void toastWarning(string $text, ?string $heading = null)
 * @method static void toastDanger(string $text, ?string $heading = null)
 * @method static void toastSaved(?string $heading = null)
 * @method static void toastInvalid(?string $heading = null)
 * @method static void showModal(string $name)
 * @method static void closeModal(string $name)
 */
class Flux extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return FluxService::class;
    }
}
