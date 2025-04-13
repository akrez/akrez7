<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TelegramTokenRule implements ValidationRule
{
    const REGEX_PATTERN = '/^\d{10,}:[A-Za-z0-9_-]{35}$/';

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
