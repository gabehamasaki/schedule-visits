<?php

namespace App\Infrastructure\Clock;

use App\Domain\Clock\ClockInterface;
use DateTimeImmutable;
use DateTimeZone;

class SystemClock implements ClockInterface
{
    public function __construct(private DateTimeZone $timezone) {}

    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', $this->timezone);
    }
}
