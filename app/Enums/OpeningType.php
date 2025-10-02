<?php

namespace App\Enums;

enum OpeningType: string
{
    case W2         = 'w2';
    case TEN99      = '1099';     // enum name can't start with a digit
    case CONTRACTOR = 'contractor';
    case PARTNER    = 'partner';

    public function label(): string
    {
        return match ($this) {
            self::W2         => 'W-2',
            self::TEN99      => '1099',
            self::CONTRACTOR => 'Contractor',
            self::PARTNER    => 'Partner',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn(self $c) => ['label' => $c->label(), 'value' => $c->value],
            self::cases()
        );
    }
}
