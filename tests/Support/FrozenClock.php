<?php

namespace Tests\Support;

use App\Domain\Clock\ClockInterface;
use DateTimeImmutable;

/**
 * Clock stuck at a fixed instant, so tests about "past" and "today" do not
 * depend on the day they happen to run.
 */
class FrozenClock implements ClockInterface
{
    private DateTimeImmutable $now;

    public function __construct(string $now = '2026-09-01 08:00', string $timezone = 'America/Sao_Paulo')
    {
        $this->now = new DateTimeImmutable($now, new \DateTimeZone($timezone));
    }

    public function now(): DateTimeImmutable
    {
        return $this->now;
    }
}
