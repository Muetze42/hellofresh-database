<?php

namespace App\Providers;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class HttpClientProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @noinspection StaticClosureCanBeUsedInspection
     */
    public function boot(): void
    {
        Http::globalOptions([
            'headers' => [
                'User-Agent' => Config::string('app.name') . ' ' . Config::string('app.env'),
                'X-Environment' => Config::string('app.env'),
            ],
        ]);

        Http::macro('proxy', function (): PendingRequest {
            $proxyUrl = Config::get('services.proxy.url');

            if (is_string($proxyUrl) && $proxyUrl !== '') {
                return Http::withOptions(['proxy' => $proxyUrl]);
            }

            return Http::withOptions([]);
        });
    }
}
