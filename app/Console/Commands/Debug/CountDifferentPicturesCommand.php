<?php

namespace App\Console\Commands\Debug;

use App\Models\Country;
use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Console\Command;
use Illuminate\Support\Number;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:count-different-pictures-command')]
class CountDifferentPicturesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:count-different-pictures-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Count different pictures';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $images = [];

        foreach (Country::all() as $country) {
            $country->switch();
            $images = array_merge(
                $images,
                Ingredient::whereNotNull('image_path')->pluck('image_path')->toArray(),
                Recipe::whereNotNull('image_path')->pluck('image_path')->toArray(),
            );
            $images = array_unique($images);
        }

        $this->info(Number::format(count($images)));
    }
}
