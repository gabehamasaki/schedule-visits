<?php

use App\Infrastructure\Http\Controllers\AppointmentController;
use App\Infrastructure\Http\Controllers\AvailabilityController;
use App\Infrastructure\Http\Controllers\VehicleController;
use FastRoute\RouteCollector;

return function (RouteCollector $router) {
    $router->addGroup('/api/v1', function (RouteCollector $router) {
        $router->addGroup('/vehicles', function (RouteCollector $router) {
            // Vehicle routes
            $router->get('', VehicleController::class . '@index');
            $router->get('/{id:\d+}', VehicleController::class . '@show');

            // Appointment route
            $router->post('/{id:\d+}/appointments', AppointmentController::class . '@store');

            // Availability route
            $router->get('/{id:\d+}/available-hours', AvailabilityController::class . '@show');
        });
    });
};
