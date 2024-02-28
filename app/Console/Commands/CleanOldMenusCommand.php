<?php

namespace App\Console\Commands;

use App\Contracts\Commands\AbstractCountryCommand;
use App\Models\Menu;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:clean-old-menus')]
class CleanOldMenusCommand extends AbstractCountryCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:clean-old-menus';

    /**
     * The console command description.
     */
    protected $description = 'Delete old menus from the database';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Menu::where('year_week', '<', now()->subDays(9)->format('YW'))->delete();
    }
}
