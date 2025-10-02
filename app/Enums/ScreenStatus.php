<?php

namespace App\Enums;

enum ScreeningPolicy: string
{
    case Off  = 'off';
    case Soft = 'soft';
    case Hard = 'hard';

    public function label(): string
    {
        return match($this) {
            self::Off  => 'Off (collect only)',
            self::Soft => 'Soft (flag only)',
            self::Hard => 'Hard (auto-fail)',
        };
    }

    public static function options(): array
    {
        return [
            ['label' => self::Off->label(),  'value' => self::Off->value],
            ['label' => self::Soft->label(), 'value' => self::Soft->value],
            ['label' => self::Hard->label(), 'value' => self::Hard->value],
        ];
    }
}