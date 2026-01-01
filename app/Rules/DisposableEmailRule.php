<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;
use JsonException;

class DisposableEmailRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=):PotentiallyTranslatedString  $fail
     *
     * @throws JsonException
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $file = config('filesystems.files.disposable_emails');

        if (! is_string($file)) {
            return;
        }

        if (! is_file($file)) {
            return;
        }

        $contents = file_get_contents($file);

        if ($contents === false) {
            return;
        }

        if (! is_string($value)) {
            $fail('validation.custom.disposable_email')->translate(['attribute' => $value]);
        }

        $domains = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

        $value = Str::lower(trim((string) $value));
        $parts = explode('@', $value);

        if (count($parts) !== 2 || in_array($parts[1], $domains, true)) {
            $fail('validation.custom.disposable_email')->translate(['attribute' => $value]);
        }
    }
}
