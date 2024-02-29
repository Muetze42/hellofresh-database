<?php

namespace App\Console\Commands\Assets;

use App\Models\Country;
use App\Models\Ingredient;
use App\Models\Recipe;
use GDText\Box;
use GDText\TailwindColor;
use Illuminate\Console\Command;
use Illuminate\Support\Number;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:assets:generate-social-preview')]
class GenerateSocialPreview extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:assets:generate-social-preview';

    /**
     * The console command description.
     */
    protected $description = 'Generate a social preview image with current database stats';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $file = resource_path('assets/social-preview.png');
        $image = imagecreatefrompng($file);

        $box = new Box($image);
        $box->setFontFace(resource_path('fonts/inter-var/ttf/Inter-Bold.ttf'));
        $box->setFontColor(new TailwindColor('gray', 100));
        $box->setFontSize(90);
        $box->setBox(80, 110, 1120, 100);
        $box->setTextAlign('center');
        $box->draw('HelloFresh Database');

        $boxY = 220;
        $padding = 70;
        $fontSize = 60;

        $box = new Box($image);
        $box->setFontFace(resource_path('fonts/inter-var/ttf/Inter-Regular.ttf'));
        $box->setFontColor(new TailwindColor('gray', 100));
        $box->setFontSize($fontSize);
        $box->setBox(80, $boxY, 1120, 800);
        $box->setTextAlign('center');
        $box->draw(Country::count() . ' Countries');

        $boxY = $boxY + $padding;

        $box = new Box($image);
        $box->setFontFace(resource_path('fonts/inter-var/ttf/Inter-Regular.ttf'));
        $box->setFontColor(new TailwindColor('gray', 100));
        $box->setFontSize($fontSize);
        $box->setBox(80, $boxY, 1120, 800);
        $box->setTextAlign('center');
        $box->draw(Country::pluck('locales')->flatten()->unique()->count() . ' Languages');

        $boxY = $boxY + $padding;

        $recipes = $ingredients = [];

        foreach (Country::all() as $country) {
            $country->switch($country->locales[0]);
            $recipes = array_merge($recipes, Recipe::pluck('id')->toArray());
            $ingredients = array_merge($ingredients, Ingredient::pluck('id')->toArray());
        }

        $recipes = array_unique($recipes);
        $ingredients = array_unique($ingredients);

        $box = new Box($image);
        $box->setFontFace(resource_path('fonts/inter-var/ttf/Inter-Regular.ttf'));
        $box->setFontColor(new TailwindColor('gray', 100));
        $box->setFontSize($fontSize);
        $box->setBox(80, $boxY, 1120, 800);
        $box->setTextAlign('center');
        $box->draw(Number::format(count($ingredients)) . ' Ingredients');

        $boxY = $boxY + $padding;

        $box = new Box($image);
        $box->setFontFace(resource_path('fonts/inter-var/ttf/Inter-Regular.ttf'));
        $box->setFontColor(new TailwindColor('gray', 100));
        $box->setFontSize($fontSize);
        $box->setBox(80, $boxY, 1120, 800);
        $box->setTextAlign('center');
        $box->draw(Number::format(count($recipes)) . ' Recipes');

        imagepng($image, public_path('assets/social-preview.png'), 1);
    }
}
