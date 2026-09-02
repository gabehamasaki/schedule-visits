<?php

use FastRoute\RouteCollector;

return function (RouteCollector $router) {
    $router->addGroup('/api/v1', function (RouteCollector $router) {
        // Define your API routes here
    });
};
