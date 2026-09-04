<?php

namespace App\Domain\Clock;

interface ClockInterface
{
    public function now(): \DateTimeImmutable;
}
