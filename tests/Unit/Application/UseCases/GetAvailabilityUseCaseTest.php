<?php

namespace Tests\Unit\Application\UseCases;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Application\DTOs\AvailabilityResponseDTO;
use App\Application\DTOs\GetAvailabilityDTO;
use App\Application\UseCases\GetAvailabilityUseCase;
use App\Domain\Repositories\AvailabilityRepositoryInterface;
use Tests\Support\FrozenClock;

class GetAvailabilityUseCaseTest extends TestCase
{
    private MockObject&AvailabilityRepositoryInterface $availabilityRepoMock;

    protected function setUp(): void
    {
        $this->availabilityRepoMock = $this->createMock(AvailabilityRepositoryInterface::class);
    }

    public function testItReturnsEveryUpcomingDayWithASingleQuery(): void
    {
        // A whole horizon of days must cost one query, not one query per day
        $this->availabilityRepoMock->expects($this->once())
            ->method('findAvailableSlots')
            ->with(1, '2026-09-01')
            ->willReturn([
                '2026-09-02' => ['09:00', '10:00'],
                '2026-09-03' => [],
            ]);

        $useCase = new GetAvailabilityUseCase($this->availabilityRepoMock, new FrozenClock('2026-09-01 08:00'));

        $response = $useCase->execute(new GetAvailabilityDTO(vehicleId: 1));

        $this->assertInstanceOf(AvailabilityResponseDTO::class, $response);
        $this->assertCount(2, $response->days);
        $this->assertEquals('2026-09-02', $response->days[0]->date);
        $this->assertEquals(['09:00', '10:00'], $response->days[0]->availableHours);

        // A fully booked day is kept with an empty list, so the client can disable it
        $this->assertEquals('2026-09-03', $response->days[1]->date);
        $this->assertEquals([], $response->days[1]->availableHours);
    }

    public function testItDropsHoursAlreadyPassedForToday(): void
    {
        $this->availabilityRepoMock->method('findAvailableSlots')
            ->willReturn([
                '2026-09-01' => ['09:00', '13:00', '14:00', '15:00'],
                '2026-09-02' => ['09:00'],
            ]);

        $useCase = new GetAvailabilityUseCase($this->availabilityRepoMock, new FrozenClock('2026-09-01 13:30'));

        $response = $useCase->execute(new GetAvailabilityDTO(vehicleId: 1));

        $this->assertEquals(['14:00', '15:00'], $response->days[0]->availableHours);

        // Later days keep every hour, no matter the current time
        $this->assertEquals(['09:00'], $response->days[1]->availableHours);
    }

    public function testItReturnsASingleDayWhenADateIsGiven(): void
    {
        $this->availabilityRepoMock->expects($this->once())
            ->method('findAvailableSlotsForDate')
            ->with(1, '2026-09-10')
            ->willReturn(['09:00', '11:00']);

        $this->availabilityRepoMock->expects($this->never())->method('findAvailableSlots');

        $useCase = new GetAvailabilityUseCase($this->availabilityRepoMock, new FrozenClock('2026-09-01 08:00'));

        $response = $useCase->execute(new GetAvailabilityDTO(vehicleId: 1, date: '2026-09-10'));

        $this->assertCount(1, $response->days);
        $this->assertEquals('2026-09-10', $response->days[0]->date);
        $this->assertEquals(['09:00', '11:00'], $response->days[0]->availableHours);
    }

    public function testItReturnsAnEmptyDayForAPastDateWithoutQuerying(): void
    {
        $this->availabilityRepoMock->expects($this->never())->method('findAvailableSlotsForDate');
        $this->availabilityRepoMock->expects($this->never())->method('findAvailableSlots');

        $useCase = new GetAvailabilityUseCase($this->availabilityRepoMock, new FrozenClock('2026-09-01 08:00'));

        $response = $useCase->execute(new GetAvailabilityDTO(vehicleId: 1, date: '2026-08-31'));

        $this->assertCount(1, $response->days);
        $this->assertEquals([], $response->days[0]->availableHours);
    }
}
