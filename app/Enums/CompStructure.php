<?php

namespace App\Enums;

enum CompStructure: string
{
    case SALARY            = 'salary';
    case COMMISSION        = 'commission';
    case SALARY_COMMISSION = 'salary_commission';
    case EQUITIES          = 'equities';

    public function label(): string
    {
        return match ($this) {
            self::SALARY            => 'Salary',
            self::COMMISSION        => 'Commission',
            self::SALARY_COMMISSION => 'Salary + Commission',
            self::EQUITIES          => 'Equities',
        };
    }

    /** For selects */
    public static function options(): array
    {
        return array_map(
            fn(self $c) => ['label' => $c->label(), 'value' => $c->value],
            self::cases()
        );
    }
}
