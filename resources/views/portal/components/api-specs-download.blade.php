@php
  use Illuminate\Support\Facades\Storage;

  $hasOpenApiSpec = Storage::disk('local')->exists('api-docs/openapi/openapi.json');
  $hasPostmanCollection = Storage::disk('local')->exists('api-docs/postman/collection.json');
  $hasAnySpec = $hasOpenApiSpec || $hasPostmanCollection;
  $isAuthenticated = auth()->check();
@endphp

@if($hasAnySpec)
  <flux:card>
    <flux:heading size="lg">API Specifications</flux:heading>
    <flux:text class="mt-ui">
      Download the API specification files to import into your favorite API client.
    </flux:text>
    <div class="mt-section flex flex-wrap gap-ui">
      @if($hasOpenApiSpec)
        <flux:button
          :href="$isAuthenticated ? route('portal.docs.download.openapi') : null"
          icon="file-down"
          :disabled="!$isAuthenticated"
        >
          OpenAPI 3.0 (Swagger)
        </flux:button>
      @endif
      @if($hasPostmanCollection)
        <flux:button
          :href="$isAuthenticated ? route('portal.docs.download.postman') : null"
          icon="file-down"
          :disabled="!$isAuthenticated"
        >
          Postman Collection
        </flux:button>
      @endif
    </div>
    @guest
      <flux:text class="mt-section text-sm text-zinc-500 dark:text-zinc-400">
        <flux:link href="{{ route('portal.login') }}" wire:navigate>Sign in</flux:link> to download the specification files.
      </flux:text>
    @endguest
  </flux:card>
@endif
