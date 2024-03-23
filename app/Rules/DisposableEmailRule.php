<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DisposableEmailRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $path = 'disposable-email-domains.json';

        if (Storage::missing($path)) {
            Artisan::call('app:update-disposable-email-domains');
        }

        if (Storage::exists($path)) {
            $domains = Storage::json($path);
            $value = Str::lower(trim($value));
            $parts = explode('@', $value);
            if (empty($parts[1]) || in_array($parts[1], $domains)) {
                $fail('Disposable and temporary email addresses are not allowed.')->translate();
            }
        }
    }
}
