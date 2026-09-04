<?php

use App\Application\UseCases\GetAllVehiclesUseCase;
use App\Application\UseCases\GetAvailabilityUseCase;
use App\Application\UseCases\GetVehicleUseCase;
use App\Application\UseCases\ScheduleVisitUseCase;
use App\Domain\Clock\ClockInterface;
use App\Domain\Repositories\AppointmentRepositoryInterface;
use App\Domain\Repositories\AvailabilityRepositoryInterface;
use App\Domain\Repositories\VehicleRepositoryInterface;
use App\Domain\ValueObjects\BusinessHours;
use App\Infrastructure\Clock\SystemClock;
use App\Infrastructure\Database\PdoConnection;
use App\Infrastructure\Repositories\PdoAppointmentRepository;
use App\Infrastructure\Repositories\PdoAvailabilityRepository;
use App\Infrastructure\Repositories\PdoVehicleRepository;
use Psr\Container\ContainerInterface;

return [
    PDO::class => function (ContainerInterface $container) {
        return PdoConnection::getInstance();
    },

    'schedule' => function (ContainerInterface $container) {
        return require __DIR__ . '/schedule.php';
    },

    ClockInterface::class => function (ContainerInterface $container) {
        return new SystemClock(new DateTimeZone($container->get('schedule')['timezone']));
    },

    // The default grid, used to generate the persisted schedule
    BusinessHours::class => function (ContainerInterface $container) {
        $schedule = $container->get('schedule');

        return BusinessHours::fromRange(
            $schedule['first_slot'],
            $schedule['last_slot'],
            $schedule['slot_minutes'],
        );
    },

    // Use Cases
    GetVehicleUseCase::class => \DI\autowire(GetVehicleUseCase::class),
    GetAllVehiclesUseCase::class => \DI\autowire(GetAllVehiclesUseCase::class),
    GetAvailabilityUseCase::class => \DI\autowire(GetAvailabilityUseCase::class),
    ScheduleVisitUseCase::class => \DI\autowire(ScheduleVisitUseCase::class),

    // Repositories
    AppointmentRepositoryInterface::class => \DI\autowire(PdoAppointmentRepository::class),
    AvailabilityRepositoryInterface::class => \DI\autowire(PdoAvailabilityRepository::class),
    VehicleRepositoryInterface::class => \DI\autowire(PdoVehicleRepository::class),
];
