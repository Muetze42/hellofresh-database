<?php

namespace App\Livewire\User;

use App\Livewire\AbstractComponent;
use App\Livewire\Concerns\WithLocalizedContextTrait;
use Illuminate\Contracts\View\View as ViewInterface;

class UserAccount extends AbstractComponent
{
    use WithLocalizedContextTrait;

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): ViewInterface
    {
        return view('livewire.user.user-account');
    }
}
