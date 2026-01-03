<div class="space-y-section">
    <div>
        <flux:heading size="xl">{{ $title }}</flux:heading>
        <flux:text class="mt-ui">{{ $description }}</flux:text>
    </div>

    <flux:card>
        @foreach($endpoints as $endpoint)
            <div class="@if(!$loop->first) mt-section @endif border-l-4 border-green-500 pl-section">
                <code class="text-sm font-mono">
                    <span class="text-green-600 font-semibold">{{ $endpoint['method'] }}</span>
                    {{ $endpoint['path'] }}
                </code>
                @if(!empty($endpoint['description']))
                    <flux:text class="mt-ui text-sm">{{ $endpoint['description'] }}</flux:text>
                @endif
            </div>
        @endforeach
    </flux:card>

    @if(!empty($queryParams))
        <flux:card>
            <flux:heading size="lg">Query Parameters</flux:heading>

            <flux:table class="mt-section">
                <flux:table.columns>
                    <flux:table.column class="ui-text-subtle">Parameter</flux:table.column>
                    <flux:table.column class="ui-text-subtle">Type</flux:table.column>
                    <flux:table.column class="ui-text-subtle">Description</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($queryParams as $param)
                        <flux:table.row wire:key="param-{{ $param['name'] }}">
                            <flux:table.cell class="font-mono text-sm">{{ $param['name'] }}</flux:table.cell>
                            <flux:table.cell>{{ $param['type'] }}</flux:table.cell>
                            <flux:table.cell>{{ $param['description'] }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>
    @endif

    <flux:card>
        <flux:heading size="lg">Response Fields</flux:heading>

        <flux:table class="mt-section">
            <flux:table.columns>
                <flux:table.column class="ui-text-subtle">Field</flux:table.column>
                <flux:table.column class="ui-text-subtle">Type</flux:table.column>
                <flux:table.column class="ui-text-subtle">Description</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach($responseFields as $field)
                    <flux:table.row wire:key="field-{{ $field['name'] }}">
                        <flux:table.cell class="font-mono text-sm">{{ $field['name'] }}</flux:table.cell>
                        <flux:table.cell>{{ $field['type'] }}</flux:table.cell>
                        <flux:table.cell>{{ $field['description'] }}</flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <flux:card>
        <flux:heading size="lg">Example Request</flux:heading>
        <pre class="mt-section p-section bg-zinc-900 text-zinc-100 rounded-lg text-sm overflow-x-auto"><code>{{ $exampleRequest }}</code></pre>
    </flux:card>

    @if($isPreRelease)
        <flux:callout icon="triangle-alert" color="amber">
            <flux:callout.heading>Pre-Release API (v{{ $version }})</flux:callout.heading>
            <flux:callout.text>
                This API is currently in pre-release. Endpoints, response formats, and features may change without notice until version 1.0.0 is released. Use in production at your own risk.
            </flux:callout.text>
        </flux:callout>
    @endif
</div>
