<?php

namespace App\Services;

use Flux\Flux;
use Illuminate\Http\Request;

class FluxService
{
    public function __construct(protected Request $request)
    {
        //
    }

    /**
     * Show a toast notification.
     */
    public function toast(string $text, ?string $heading = null, ?string $variant = null): void
    {
        Flux::toast(
            text: $text,
            heading: $heading,
            duration: 2_500,
            variant: $variant,
        );
    }

    /**
     * Show a toast success notification.
     */
    public function toastSuccess(string $text, ?string $heading = null): void
    {
        $this->toast($text, $heading, 'success');
    }

    /**
     * Show a toast success notification.
     */
    public function toastSaved(?string $message = null, ?string $heading = null): void
    {
        if (in_array($message, [null, '', '0'], true)) {
            $message = (string) __('Your changes have been saved');
        }

        $this->toast($message, $heading, 'success');
    }

    /**
     * Show a toast success notification.
     */
    public function toastFailed(?string $message = null, ?string $heading = null): void
    {
        if (in_array($message, [null, '', '0'], true)) {
            $message = (string) __('Whoops!'); // Todo
        }

        $this->toast($message, $heading, 'danger');
    }

    /**
     * Display a modal by dispatching a modal-show event with the given name.
     */
    public function showModal(string $name): void
    {
        resolve('livewire')->current()->dispatch('modal-show', name: $name);
    }

    /**
     * Close a modal by dispatching a modal-show event with the given name.
     */
    public function closeModal(string $name): void
    {
        resolve('livewire')->current()->dispatch('modal-close', name: $name);
    }

    /**
     * Show a toast success notification.
     */
    public function toastInvalid(?string $heading = null): void
    {
        $this->toast(__('Some fields are invalid'), $heading, 'danger');
    }

    /**
     * Show a toast warning notification.
     */
    public function toastWarning(string $text, ?string $heading = null): void
    {
        $this->toast($text, $heading, 'warning');
    }

    /**
     * Show a toast danger notification.
     */
    public function toastDanger(string $text, ?string $heading = null): void
    {
        $this->toast($text, $heading, 'danger');
    }
}
