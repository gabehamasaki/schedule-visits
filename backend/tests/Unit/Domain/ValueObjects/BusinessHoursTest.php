<?php

namespace Test\Unit\Domain\ValueObjects;

use App\Domain\ValueObjects\BusinessHours;
use PHPUnit\Framework\TestCase;

class BusinessHoursTest extends TestCase
{
    public function testItGeneratesSlotsCorrectly(): void
    {
        $businessHours = BusinessHours::fromRange('09:00', '12:00', 30);
        $expectedSlots = ['09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00'];
        $this->assertEquals($expectedSlots, $businessHours->slots());
    }

    public function testItThrowsExceptionForInvalidSlotDuration(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        BusinessHours::fromRange('09:00', '12:00', 0);
    }

    public function testItThrowsExceptionForInvalidTimeRange(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        BusinessHours::fromRange('12:00', '09:00', 30);
    }
}
