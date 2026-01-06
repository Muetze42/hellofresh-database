<?php

namespace App\Livewire\Web\Auth;

use App\Livewire\AbstractComponent;
use App\Livewire\Concerns\ManagesUserProfileTrait;
use App\Livewire\Web\Concerns\WithLocalizedContextTrait;
use Illuminate\Contracts\View\View as ViewInterface;

class AccountSetting extends AbstractComponent
{
    use ManagesUserProfileTrait;
    use WithLocalizedContextTrait;

    public function mount(): void
    {
        $this->mountUserProfile();
    }

    public function render(): ViewInterface
    {
        return view('web::livewire.auth.settings');
    }
}
