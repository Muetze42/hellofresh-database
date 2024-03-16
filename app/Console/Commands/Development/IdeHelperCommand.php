<?php

namespace App\Console\Commands\Development;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'helper')]
class IdeHelperCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'helper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update `Laravel IDE Helper`';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->call(
            'ide-helper:models',
            [
                '--write'   => false,
                '--nowrite' => true,
            ]
        );
        $this->call('ide-helper:generate');
        $this->call('ide-helper:meta');

        if (class_exists('Tutorigo\LaravelMacroHelper\IdeMacrosServiceProvider')) {
            $this->call('ide-helper:macros');
        }
    }
}
