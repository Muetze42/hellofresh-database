<?php

namespace App\Observers;

use App\Jobs\ClearStatisticsCacheJob;
use App\Models\EmailVerification;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        ClearStatisticsCacheJob::dispatch();
    }

    /**
     * Handle the User "updating" event.
     */
    public function updating(User $user): void
    {
        if (! $user->isDirty('email')) {
            return;
        }

        $emailVerification = $user->emailVerifications()->whereLike('email', $user->email)->first();

        if (! $emailVerification instanceof EmailVerification) {
            if ($user->email_verified_at !== null) {
                $user->emailVerifications()->updateOrCreate(
                    ['email' => $user->getOriginal('email')],
                    ['verified_at' => $user->email_verified_at]
                );
            }

            $user->email_verified_at = null;

            return;
        }

        $user->email_verified_at = $emailVerification->verified_at;
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if (! $user->wasChanged('country_code')) {
            return;
        }

        ClearStatisticsCacheJob::dispatch();
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        ClearStatisticsCacheJob::dispatch();
    }
}
