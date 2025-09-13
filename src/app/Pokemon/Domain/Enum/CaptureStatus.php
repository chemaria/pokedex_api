<?php

namespace App\Pokemon\Domain\Enum;

enum CaptureStatus: string
{
    case WILD = 'wild';
    case CAPTURED = 'captured';

    public function isCaptured(): bool
    {
        return $this === self::CAPTURED;
    }

    public function capture(): self
    {
        return self::CAPTURED;
    }
}
