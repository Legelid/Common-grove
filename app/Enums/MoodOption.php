<?php

declare(strict_types=1);

namespace App\Enums;

enum MoodOption: string
{
    case Happy    = 'happy';
    case Focused  = 'focused';
    case Relaxed  = 'relaxed';
    case Tired    = 'tired';
    case Creative = 'creative';
    case Social   = 'social';
    case Quiet    = 'quiet';
    case Gaming   = 'gaming';

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
