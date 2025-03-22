<?php

namespace App\Enums;

use Illuminate\Support\Facades\Lang;

trait Enum
{
    public function trans()
    {
        $key = 'enums'.'.'.class_basename(static::class).'.'.$this->value;
        if (Lang::has($key)) {
            return __($key);
        }

        $key = 'enums'.'.'.$this->value;
        if (Lang::has($key)) {
            return __($key);
        }

        return $this->value;
    }

    public static function toArray(): array
    {
        return once(function () {
            $result = [];
            foreach (self::cases() as $case) {
                $result[$case->value] = $case->trans();
            }

            return $result;
        });
    }

    public static function values(): array
    {
        return array_keys(static::toArray());
    }

    public function toResource()
    {
        return [
            'value' => $this->value,
            'trans' => $this->trans(),
        ];
    }
}
