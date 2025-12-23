<flux:modal
    name="confirm-action"
    class="min-w-[22rem]"
    x-data
    x-on:confirm-action.window="
        $el.querySelector('[data-confirm-title]').textContent = $event.detail.title || '{{ __('Confirm Action') }}';
        $el.querySelector('[data-confirm-message]').textContent = $event.detail.message || '{{ __('Are you sure?') }}';
        $el.querySelector('[data-confirm-button]').textContent = $event.detail.confirmText || '{{ __('Confirm') }}';
        window._confirmAction = $event.detail.onConfirm;
        $flux.modal('confirm-action').show();
    "
>
    <div class="flex flex-col gap-section">
        <div>
            <flux:heading size="lg" data-confirm-title>{{ __('Confirm Action') }}</flux:heading>
            <flux:text class="mt-ui" data-confirm-message>{{ __('Are you sure?') }}</flux:text>
        </div>

        <div class="flex gap-ui">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
            </flux:modal.close>
            <flux:button
                variant="danger"
                data-confirm-button
                x-on:click="if (window._confirmAction) { window._confirmAction(); window._confirmAction = null; } $flux.modal('confirm-action').close();"
            >
                {{ __('Confirm') }}
            </flux:button>
        </div>
    </div>
</flux:modal>
