<?php

namespace App\Console\Commands\DataMaintenance;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'data-maintenance:update-disposable-email-domains')]
class UpdateDisposableEmailDomainsCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data-maintenance:update-disposable-email-domains';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update disposable email domains';

    /**
     * The source of the data.
     */
    protected string $source = 'https://cdn.jsdelivr.net/gh/disposable/disposable-email-domains@master/domains.json';

    /**
     * Execute the console command.
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function handle(): void
    {
        $response = Http::get($this->source);
        $response->throw();

        $body = $response->body();

        if (! Str::isJson($body)) {
            throw new RuntimeException('Invalid JSON response from source');
        }

        Storage::put('disposable-email-domains.json', $body);
    }
}
