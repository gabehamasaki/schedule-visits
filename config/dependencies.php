<?php

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

    AppointmentRepositoryInterface::class => \DI\autowire(PdoAppointmentRepository::class),
    VehicleRepositoryInterface::class => \DI\autowire(PdoVehicleRepository::class),
];
