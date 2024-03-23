<?php

namespace App\Console\Commands\Development;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Foundation\Console\ConsoleMakeCommand as Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:command')]
class ConsoleMakeCommand extends Command
{
    /**
     * Replace the class name for the given stub.
     *
     * @param string $stub
     * @param string $name
     */
    protected function replaceClass($stub, $name): string
    {
        $stub = GeneratorCommand::replaceClass($stub, $name);

        $command = $this->option('command');

        if (!$command) {
            $command = Str::of($this->rootNamespace())->classBasename()->lower()->value();

            if (str_ends_with($name, 'Command')) {
                $name = substr($name, 0, -7);
            }

            $defaultNamespace = str_replace('\\\\', '\\', $this->getDefaultNamespace($this->rootNamespace()));
            $commandClass = trim(str_replace($defaultNamespace, '', $name), '\\');
            $levels = explode('\\', $commandClass);

            foreach ($levels as $level) {
                $command .= ':' . Str::kebab($level);
            }
        }

        return str_replace(['dummy:command', '{{ command }}'], $command, $stub);
    }
}
