<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:update-disposable-email-domains')]
class UpdateDisposableEmailDomainsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-disposable-email-domains';

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

    public function __construct()
    {
        @set_time_limit(0);
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Storage::put(
            'disposable-email-domains.json',
            Http::get($this->source)->body()
        );

        $this->components->info('Successfully updated disposable email domains');
    }
}
