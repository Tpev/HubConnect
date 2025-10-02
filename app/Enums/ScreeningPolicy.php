<?php

namespace App\Enums;

enum ScreeningPolicy: string
{
    case Off  = 'off';
    case Soft = 'soft';
    case Hard = 'hard';

    public static function options(): array
    {
        return [
            ['label' => 'Off (no filtering)', 'value' => self::Off->value],
            ['label' => 'Soft (flag only)',   'value' => self::Soft->value],
            ['label' => 'Hard (auto-fail)',   'value' => self::Hard->value],
        ];
    }
}
