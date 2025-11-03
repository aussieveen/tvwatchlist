<?php

declare(strict_types=1);

namespace App\Traits;

trait HasValues
{
    public static function values(): array
    {
        return array_map(
            fn (self $case) => $case->value,
            self::cases()
        );
    }
}
