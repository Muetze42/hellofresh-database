<?php

namespace App\Contracts\Commands;

use App\Models\Country;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCountryCommand extends Command
{
    protected Country $country;

    protected bool $considerLanguages = true;

    public function __construct()
    {
        parent::__construct();
        $this->addAdditionalOption(
            'countries',
            'Run this command only for specific countries. [ID, comma separated]'
        );
    }

    protected function addAdditionalOption(
        string $name,
        string $description = '',
        string|array|null $shortcut = null,
        ?int $mode = InputOption::VALUE_OPTIONAL,
        string|bool|int|float|array|null $default = null
    ): void {
        $this->getDefinition()->addOption(
            new InputOption(
                $name,
                $shortcut,
                $mode,
                $description,
                $default,
            )
        );
    }

    /**
     * Execute the console command.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = 0;
        $countries = Country::query();

        if ($countryIds = $this->option('countries')) {
            $countries->whereIn('id', explode(',', $countryIds));
        }

        foreach ($countries->get() as $country) {
            $locales = $country->locales;
            if (!$this->considerLanguages) {
                $locales = [$locales[0]];
            }
            foreach ($locales as $locale) {
                $this->code = $country;
                $this->code->switch($locale);

                $exitCode = parent::execute($input, $output);
            }
        }

        return $exitCode;
    }
}
