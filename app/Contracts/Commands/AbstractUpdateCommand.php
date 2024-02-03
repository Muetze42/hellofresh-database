<?php

namespace App\Contracts\Commands;

class AbstractUpdateCommand extends AbstractCountryCommand
{
    /**
     * The console command description.
     */
    protected $description = 'Synchronize %s with the HelloFresh database';

    public function __construct()
    {
        $this->description = sprintf($this->description, substr(class_basename(get_called_class()), 6, -7));
        parent::__construct();
        $this->addAdditionalOption(
            'limit',
            'Determine how many items should be updated. Value is calculated using the Country `take` column'
        );
    }
}
