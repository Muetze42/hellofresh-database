<?php

namespace App\Livewire\Portal;

use App\Livewire\Web\Auth\AccountSetting;
use Livewire\Attributes\Layout;
use Override;

#[Layout('portal::components.layouts.app')]
class Profile extends AccountSetting
{
    #[Override]
    protected function afterEmailChanged(): void
    {
        $this->redirect(route('portal.profile'), navigate: true);
    }
}
