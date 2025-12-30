<?php

namespace App\Console\Commands\User;

use App\Models\EmailVerification;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'prune-expired-email-verifications')]
class PruneExpiredEmailVerificationsCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prune-expired-email-verifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune expired email verifications';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        EmailVerification::where('verified_at', '<', now()->subMonths(6))
            ->delete();
    }
}
