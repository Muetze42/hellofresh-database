<?php

namespace App\Console\Commands\Users;

use App\Contracts\Commands\TableHelpersTrait;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:users:list-admins')]
class ListAdminsCommand extends Command
{
    use TableHelpersTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:users:list-admins';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display a listing of the administrators of the application';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $admins = User::whereIsAdmin(true)->get(['id', 'name', 'email']);

        if (!$admins->count()) {
            $this->components->info('This application has no administrators');

            return;
        }

        $this->components->info('Following administrators are available in this application:');

        $this->table(
            ['ID', 'Name', 'Email'],
            Arr::map($admins->toArray(), fn (array $user) => Arr::only($user, ['id', 'name', 'email']))
        );
    }
}
