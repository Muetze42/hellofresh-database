<?php

namespace App\Console\Commands\Fix;

use App\Contracts\Commands\AbstractCountryCommand;
use App\Models\Menu;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'fix:delete-empty-menus')]
class DeleteEmptyMenusCommand extends AbstractCountryCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'xs';

    /**
     * The console command description.
     */
    protected $description = 'Delete menus without recipes';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Menu::whereDoesntHave('recipes')->delete();
    }
}
