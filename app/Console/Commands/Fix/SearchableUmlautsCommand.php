<?php

namespace App\Console\Commands\Fix;

use App\Contracts\Commands\AbstractCountryCommand;
use App\Contracts\Models\AbstractTranslatableModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use SplFileInfo;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Finder\Finder;

#[AsCommand(name: 'fix:searchable-umlauts')]
class SearchableUmlautsCommand extends AbstractCountryCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'fix:searchable-umlauts';

    /**
     * The console command description.
     */
    protected $description = 'Fix umlauts problem';

    protected array $translateAbleModels = [];

    public function __construct()
    {
        parent::__construct();

        $namespace = app()->getNamespace();

        foreach (Finder::create()->in(app_path('Models'))->files() as $file) {
            $model = $this->modelFromFile($file, $namespace);
            if (is_subclass_of($model, AbstractTranslatableModel::class)) {
                $this->translateAbleModels[] = $model;
            }
        }
    }

    /**
     * Extract the model class name from the given file path.
     */
    protected function modelFromFile(SplFileInfo $file, string $namespace): string
    {
        return $namespace . str_replace(
            ['/', '.php'],
            ['\\', ''],
            Str::after($file->getRealPath(), realpath(app_path()) . DIRECTORY_SEPARATOR)
        );
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        foreach ($this->translateAbleModels as $model) {
            $model = app($model);
            $translatable = $model->translatable;
            $model::each(function (Model $model) use ($translatable) {
                /* @var \App\Models\Allergen|\App\Models\Ingredient $model */
                $data = Arr::only($model->toArray(), $translatable);

                $data = array_map(fn (?string $value) => is_null($value) ? null : $value . '-', $data);
                $model->update($data);

                $data = array_map(fn (?string $value) => is_null($value) ? null : substr($value, 0, -1), $data);
                $model->update($data);
            });
        }
    }
}
