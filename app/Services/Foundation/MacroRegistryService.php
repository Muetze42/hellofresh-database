<?php

namespace App\Services\Foundation;

class MacroRegistryService
{
    /**
     * @param  class-string  $macroableClass
     * @param  class-string[]  $macros
     */
    public function macrosFor(string $macroableClass, array $macros): void
    {
        foreach ($macros as $macro) {
            $this->macro($macro, $macroableClass);
        }
    }

    /**
     * Register a custom macro using invokable class.
     *
     * @param  class-string  $macroClass
     * @param  class-string  $macroableClass
     */
    public function macro(string $macroClass, string $macroableClass): void
    {
        $method = lcfirst(class_basename($macroClass));

        if (str_ends_with($method, 'Macro') && strlen($method) > 5) {
            $method = substr($method, 0, -5);
        }

        if (! method_exists($macroClass, '__invoke')) {
            return;
        }

        $macroableClass::macro($method, new $macroClass()());
    }

    /**
     * Register an array of custom macros using invokable class.
     *
     * @param  array<class-string, class-string>  $macroMacroableClasses
     */
    public function macros(array $macroMacroableClasses): void
    {
        foreach ($macroMacroableClasses as $macroClass => $macroableClass) {
            $this->macro($macroClass, $macroableClass);
        }
    }
}
