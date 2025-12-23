<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;

class DisposableEmailRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=):PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $path = 'disposable-email-domains.json';

        if (Storage::missing($path)) {
            Log::error('Disposable email domains file not found: ' . $path);

            return;
        }

        if (! is_string($value)) {
            $fail('validation.custom.disposable_email')->translate(['attribute' => $value]);
        }

        $domains = Storage::json($path);

        if ($domains === null) {
            Log::error('Failed to parse disposable email domains file: ' . $path);

            return;
        }

        $value = Str::lower(trim((string) $value));
        $parts = explode('@', $value);

        if (count($parts) !== 2 || in_array($parts[1], $domains, true)) {
            $fail('validation.custom.disposable_email')->translate(['attribute' => $value]);
        }
    }
}
