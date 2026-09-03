<?php

namespace Tests\Unit\Application\UseCases;

use PHPUnit\Framework\TestCase;
use App\Application\UseCases\GetAvailableHoursUseCase;
use App\Domain\Repositories\AppointmentRepositoryInterface;
use App\Application\DTOs\GetAvailableHoursDTO;
use App\Application\DTOs\AvailableHoursResponseDTO;

class GetAvailableHoursUseCaseTest extends TestCase
{
    public function testItReturnsOnlyAvailableHoursExcludingBookedOnes(): void
    {
        // 1. Arrange
        $repositoryMock = $this->createMock(AppointmentRepositoryInterface::class);

        // We simulate that 10:00 and 14:00 are already booked in the database
        $repositoryMock->expects($this->once())
            ->method('getBookedHours')
            ->with(1, '2026-09-02')
            ->willReturn(['10:00:00', '14:00:00']);

        $useCase = new GetAvailableHoursUseCase($repositoryMock);

        $dto = new GetAvailableHoursDTO(
            vehicleId: 1,
            date: '2026-09-02',
        );

        // 2. Act
        $response = $useCase->execute($dto);

        // 3. Assert
        $this->assertInstanceOf(AvailableHoursResponseDTO::class, $response);

        $availableHours = $response->availableHours;

        $this->assertNotContains('10:00', $availableHours);
        $this->assertNotContains('14:00', $availableHours);
        $this->assertContains('09:00', $availableHours);
        $this->assertContains('15:00', $availableHours);
    }
}
