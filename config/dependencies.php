<?php

use App\Application\UseCases\GetAllVehiclesUseCase;
use App\Application\UseCases\GetAvailableHoursUseCase;
use App\Application\UseCases\GetVehicleUseCase;
use App\Application\UseCases\ScheduleVisitUseCase;
use App\Domain\Repositories\AppointmentRepositoryInterface;
use App\Domain\Repositories\VehicleRepositoryInterface;
use App\Infrastructure\Database\PdoConnection;
use App\Infrastructure\Repositories\PdoAppointmentRepository;
use App\Infrastructure\Repositories\PdoVehicleRepository;
use Psr\Container\ContainerInterface;

return [
    PDO::class => function (ContainerInterface $container) {
        return PdoConnection::getInstance();
    },

    // Use Cases
    GetVehicleUseCase::class => \DI\autowire(GetVehicleUseCase::class),
    GetAllVehiclesUseCase::class => \DI\autowire(GetAllVehiclesUseCase::class),
    GetAvailableHoursUseCase::class => \DI\autowire(GetAvailableHoursUseCase::class),
    ScheduleVisitUseCase::class => \DI\autowire(ScheduleVisitUseCase::class),

    AppointmentRepositoryInterface::class => \DI\autowire(PdoAppointmentRepository::class),
    VehicleRepositoryInterface::class => \DI\autowire(PdoVehicleRepository::class),
];
