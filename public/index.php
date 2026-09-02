<?php

require_once __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // 1. Load routes from separte file
    $routeDefinitionCallback = require __DIR__ . '/../routes/web.php';
    $dispatcher = FastRoute\simpleDispatcher($routeDefinitionCallback);

    // 2. Fetch method and URI from server variables
    $httpMethod = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'];

    // 3. Strip query string (?foo=bar) and decode URI
    if (false !== $pos = strpos($uri, '?')) {
        $uri = substr($uri, 0, $pos);
    }

    $uri = rawurldecode($uri);

    // 4. Dispatch the request
    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            http_response_code(404);
            echo json_encode(['error' => 'Not Found']);
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $routeInfo[1];
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed', 'allowed_methods' => $allowedMethods]);
            break;
        case FastRoute\Dispatcher::FOUND:

            $handler = $routeInfo[1]; // This is the controller and method to call
            $routerParams = $routeInfo[2]; // This is an associative array of parameters extracted from the URI

            // Split the handler into controller class and method
            list($controllerClass, $method) = explode('@', $handler);

            // Check if the controller class exists
            if (!class_exists($controllerClass)) {
                throw new Exception("Controller class $controllerClass not found.");
            }

            // Instantiate the controller
            $controlleInstance = new $controllerClass();

            // Read the request body for POST, PUT, DELETE methods
            $requestBody = [];
            if (in_array($httpMethod, ['POST', 'PUT', 'DELETE'])) {
                $requestBody = json_decode(file_get_contents('php://input'), true);
            }

            // Execute the controller method with the appropriate parameters
            $response = call_user_func_array([$controlleInstance, $method], [$requestBody, $routerParams]);

            // Send the response
            echo json_encode($response);
            break;
    }
} catch (Exception $e) {
    error_log("[Index] Bootstrap or Routing error: " . $e->getMessage());
    http_response_code(500);

    echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
}
