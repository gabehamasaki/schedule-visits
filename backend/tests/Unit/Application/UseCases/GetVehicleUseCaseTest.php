<?php

namespace Tests\Unit\Application\UseCases;

use App\Application\UseCases\GetVehicleUseCase;
use App\Domain\Exceptions\NotFoundException;
use App\Domain\Repositories\VehicleRepositoryInterface;
use PHPUnit\Framework\TestCase;

class GetVehicleUseCaseTest extends TestCase
{
    public function testItThrowsNotFoundExceptionWhenVehicleDoesNotExist(): void
    {
        $vehicleRepositoryMock = $this->createMock(VehicleRepositoryInterface::class);
        $vehicleRepositoryMock->method('findById')->willReturn(null);

        $useCase = new GetVehicleUseCase($vehicleRepositoryMock);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Vehicle not found.');

        $useCase->execute(999); // Assuming 999 is a non-existent vehicle ID
    }
}
