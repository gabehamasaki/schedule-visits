<?php

namespace Tests\Unit\Application\UseCases;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Application\DTOs\AvailabilityResponseDTO;
use App\Application\DTOs\GetAvailabilityDTO;
use App\Application\DTOs\SlotDTO;
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
            ->method('findSlots')
            ->with(1, '2026-09-01')
            ->willReturn([
                '2026-09-02' => ['09:00' => true, '10:00' => false],
                '2026-09-03' => ['09:00' => false],
            ]);

        $useCase = new GetAvailabilityUseCase($this->availabilityRepoMock, new FrozenClock('2026-09-01 08:00'));

        $response = $useCase->execute(new GetAvailabilityDTO(vehicleId: 1));

        $this->assertInstanceOf(AvailabilityResponseDTO::class, $response);
        $this->assertCount(2, $response->days);

        // A booked slot stays in the grid, flagged as unavailable
        $this->assertEquals(
            [new SlotDTO('09:00', true), new SlotDTO('10:00', false)],
            $response->days[0]->slots
        );

        // A fully booked day is kept, so the client can show it disabled
        $this->assertEquals('2026-09-03', $response->days[1]->date);
        $this->assertEquals([new SlotDTO('09:00', false)], $response->days[1]->slots);
    }

    public function testItLeavesOutHoursAlreadyPassedToday(): void
    {
        $this->availabilityRepoMock->method('findSlots')
            ->willReturn([
                '2026-09-01' => ['09:00' => true, '13:00' => true, '14:00' => true, '15:00' => false],
                '2026-09-02' => ['09:00' => true],
            ]);

        $useCase = new GetAvailabilityUseCase($this->availabilityRepoMock, new FrozenClock('2026-09-01 13:30'));

        $response = $useCase->execute(new GetAvailabilityDTO(vehicleId: 1));

        // 09:00 and 13:00 are gone, while 15:00 stays as a booked slot
        $this->assertEquals(
            [new SlotDTO('14:00', true), new SlotDTO('15:00', false)],
            $response->days[0]->slots
        );

        // Later days keep every hour, no matter the current time
        $this->assertEquals([new SlotDTO('09:00', true)], $response->days[1]->slots);
    }

    public function testItReturnsNoSlotsWhenTodayIsOver(): void
    {
        $this->availabilityRepoMock->method('findSlots')
            ->willReturn(['2026-09-01' => ['09:00' => true, '18:00' => true]]);

        $useCase = new GetAvailabilityUseCase($this->availabilityRepoMock, new FrozenClock('2026-09-01 19:00'));

        $response = $useCase->execute(new GetAvailabilityDTO(vehicleId: 1));

        // The day is kept so the client can show it disabled instead of missing
        $this->assertCount(1, $response->days);
        $this->assertEquals([], $response->days[0]->slots);
    }

    public function testItReturnsASingleDayWhenADateIsGiven(): void
    {
        $this->availabilityRepoMock->expects($this->once())
            ->method('findSlotsForDate')
            ->with(1, '2026-09-10')
            ->willReturn(['09:00' => true, '11:00' => false]);

        $this->availabilityRepoMock->expects($this->never())->method('findSlots');

        $useCase = new GetAvailabilityUseCase($this->availabilityRepoMock, new FrozenClock('2026-09-01 08:00'));

        $response = $useCase->execute(new GetAvailabilityDTO(vehicleId: 1, date: '2026-09-10'));

        $this->assertCount(1, $response->days);
        $this->assertEquals('2026-09-10', $response->days[0]->date);
        $this->assertEquals(
            [new SlotDTO('09:00', true), new SlotDTO('11:00', false)],
            $response->days[0]->slots
        );
    }

    public function testItReturnsAnEmptyDayForAPastDateWithoutQuerying(): void
    {
        $this->availabilityRepoMock->expects($this->never())->method('findSlotsForDate');
        $this->availabilityRepoMock->expects($this->never())->method('findSlots');

        $useCase = new GetAvailabilityUseCase($this->availabilityRepoMock, new FrozenClock('2026-09-01 08:00'));

        $response = $useCase->execute(new GetAvailabilityDTO(vehicleId: 1, date: '2026-08-31'));

        $this->assertCount(1, $response->days);
        $this->assertEquals([], $response->days[0]->slots);
    }
}
