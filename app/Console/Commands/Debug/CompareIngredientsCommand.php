<?php

namespace App\Console\Commands\Debug;

use App\Contracts\Commands\AbstractCountryCommand;
use App\Models\Ingredient;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:compare-ingredients-command')]
class CompareIngredientsCommand extends AbstractCountryCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:compare-ingredients-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compare country ingredients';

    protected array $data = [];

    protected array $skip = [
        '62b971eb4e59e3d7610c1a00-f94cf410.png',
        '62b971eb4e59e3d7610c1a00-0b13319b.png',
        '62e7a92bd6d6e168d90bbfa0-d4bbed24.png',
        '62e7a92bd6d6e168d90bbfa0-1e90c8fd.png',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Ingredient::whereNotNull('image_path')
            ->each(function (Ingredient $ingredient) {
                if (!isset($this->data[$ingredient->getKey()])) {
                    $this->data[$ingredient->getKey()] = $ingredient->image_path;
                } else {
                    if (
                        $this->data[$ingredient->getKey()] != $ingredient->image_path &&
                        !in_array(basename($ingredient->getKey()), $this->skip) &&
                        !in_array(basename($ingredient->image_path), $this->skip)
                    ) {
                        $file1 = 'https://img.hellofresh.com/w_96,q_auto,f_auto,c_limit,fl_lossy/hellofresh_s3' .
                            $this->data[$ingredient->getKey()];
                        $file2 = 'https://img.hellofresh.com/w_96,q_auto,f_auto,c_limit,fl_lossy/hellofresh_s3' .
                            $ingredient->image_path;
                        //$this->line($file1);
                        //$this->line($file2);
                        //die();
                        if (md5(file_get_contents($file1)) != md5(file_get_contents($file2))) {
                            $this->line($file1);
                            $this->line($file2);
                            die();
                        }
                    }
                }
            });
    }
}
