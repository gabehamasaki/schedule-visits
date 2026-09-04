<?php

use App\Infrastructure\Http\Controllers\VehicleController;
use FastRoute\RouteCollector;

return function (RouteCollector $router) {
    $router->addGroup('/api/v1', function (RouteCollector $router) {
        $router->addGroup('/vehicles', function (RouteCollector $router) {
            $router->get('', VehicleController::class . '@index');
            $router->get('/{id:\d+}', VehicleController::class . '@show');

            $router->post('/{id:\d+}/appointments', \App\Infrastructure\Http\Controllers\AppointmentController::class . '@store');
        });
    });
};
