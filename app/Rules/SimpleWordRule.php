<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SimpleWordRule implements ValidationRule
{
    const REGEX_PATTERN = '/^[a-z0-9_]*$/s';

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match(static::REGEX_PATTERN, $value)) {
            $fail(__('validation.regex', [
                'attribute' => __('validation.attributes.'.$attribute),
            ]));
        }
    }
}
