<div class="space-y-section">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">API Tokens</flux:heading>
        <flux:modal.trigger name="create-token">
            <flux:button variant="primary" icon="plus">
                Create Token
            </flux:button>
        </flux:modal.trigger>
    </div>

    @if($newToken)
        <flux:callout icon="circle-check" color="green">
            <flux:callout.heading>Token Created Successfully</flux:callout.heading>
            <flux:callout.text>
                <p class="mb-ui">Make sure to copy your new API token now. You won't be able to see it again!</p>
                <flux:input icon="key" :value="$newToken" readonly copyable />
                <div class="mt-section">
                    <flux:button size="sm" variant="ghost" wire:click="clearNewToken">
                        Dismiss
                    </flux:button>
                </div>
            </flux:callout.text>
        </flux:callout>
    @endif

    @if($tokens->isEmpty())
        <flux:card>
            <div class="text-center py-section">
                <flux:icon name="key" class="w-12 h-12 mx-auto text-zinc-400" />
                <flux:heading size="lg" class="mt-ui">No API Tokens</flux:heading>
                <flux:text class="mt-ui">
                    Create your first API token to start making authenticated requests.
                </flux:text>
                <div class="mt-section">
                    <flux:modal.trigger name="create-token">
                        <flux:button variant="primary" icon="plus">
                            Create Token
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>
        </flux:card>
    @else
        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Created</flux:table.column>
                    <flux:table.column>Expires</flux:table.column>
                    <flux:table.column>Last Used</flux:table.column>
                    <flux:table.column class="w-20"></flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($tokens as $token)
                        <flux:table.row wire:key="token-{{ $token->id }}">
                            <flux:table.cell class="font-medium">{{ $token->name }}</flux:table.cell>
                            <flux:table.cell>{{ $token->created_at->format('M d, Y H:i') }}</flux:table.cell>
                            <flux:table.cell>
                                @if($token->expires_at)
                                    <span @class(['text-red-600' => $token->expires_at->isPast()])>
                                        {{ $token->expires_at->format('M d, Y') }}
                                    </span>
                                @else
                                    Never
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>{{ $token->last_used_at?->diffForHumans() ?? 'Never' }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:button
                                    variant="ghost"
                                    size="sm"
                                    icon="trash-2"
                                    x-on:click="$dispatch('confirm-action', {
                                        title: '{{ __('Delete Token') }}',
                                        message: '{{ __('Are you sure you want to delete this API token? Any applications using this token will no longer have access.') }}',
                                        confirmText: '{{ __('Delete') }}',
                                        onConfirm: () => $wire.deleteToken({{ $token->id }})
                                    })"
                                />
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>
    @endif

    <flux:modal name="create-token" class="md:w-96">
        <form wire:submit="createToken" class="space-y-section">
            <div>
                <flux:heading size="lg">Create API Token</flux:heading>
                <flux:text class="mt-ui">
                    Give your token a descriptive name so you can identify it later.
                </flux:text>
            </div>

            <flux:input
                wire:model="tokenName"
                label="Token Name"
                placeholder="e.g., Production Server"
                required
                autofocus
            />

            <flux:select wire:model="tokenExpiration" label="Expires in" variant="listbox">
                @foreach($this->getExpirationOptions() as $days => $label)
                    <flux:select.option value="{{ $days }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>

            <div class="flex justify-end gap-ui">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">
                    Create Token
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
