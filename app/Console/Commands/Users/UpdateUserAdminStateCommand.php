<?php

namespace App\Console\Commands\Users;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:users:update-user-admin-state')]
class UpdateUserAdminStateCommand extends ListAdminsCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:users:update-user-admin-state {user : The ID of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change the admin state of a user';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $id = $this->argument('user');

        try {
            $user = User::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            $this->components->error($exception->getMessage());

            return;
        }

        $isAdmin = $this->confirm('Should this user be an admin?');

        DB::table('users')
            ->where('id', $user->getKey())
            ->update(['is_admin' => $isAdmin]);

        parent::handle();
    }
}
