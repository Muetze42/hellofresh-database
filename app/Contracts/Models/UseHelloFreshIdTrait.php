<?php

namespace App\Contracts\Models;

trait UseHelloFreshIdTrait
{
    /**
     * Initialize the trait.
     */
    protected function initializeUseHelloFreshIdTrait(): void
    {
        $this->incrementing = false;
        $this->setKeyType('string');
        $this->mergeFillable(['id']);
        $this->mergeCasts(['id' => 'string']);
    }
}
