<?php

use App\Infrastructure\Http\Controllers\VehicleController;
use FastRoute\RouteCollector;

return function (RouteCollector $router) {
    $router->addGroup('/api/v1', function (RouteCollector $router) {
        $router->get('/vehicles', VehicleController::class . '@index');
        $router->get('/vehicles/{id:\d+}', VehicleController::class . '@show');
    });
};
