<?php

namespace App\Console\Commands\Development;

use App\Models\Country;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:development:flags')]
class FlagsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:development:flags';

    /**
     * The console command description.
     */
    protected $description = 'Update the flag icons in app.scss';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $codes = array_map(fn (string $code) => '"' . Str::lower($code) . '"', Country::pluck('code')->toArray());
        $contents = [];
        $file = resource_path('scss/app.scss');
        $lines = preg_split('/\r\n|\r|\n/', trim(file_get_contents($file)));
        foreach ($lines as $line) {
            if (!Str::startsWith($line, '$flag-icons-included-countries')) {
                $contents[] = $line;
                continue;
            }
            $contents[] = '$flag-icons-included-countries: ' . implode(', ', $codes) . ';';
        }

        file_put_contents($file, implode("\n", $contents) . "\n");
    }
}
