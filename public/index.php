<?php

use App\Domain\Exceptions\DomainException;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Setup PHP-DI Container
    $containerBuilder = new DI\ContainerBuilder();
    $containerBuilder->addDefinitions(__DIR__ . '/../config/dependencies.php');
    $container = $containerBuilder->build();

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
            Response::notFound()->send();
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $routeInfo[1];
            Response::methodNotAllowed($allowedMethods)->send();
            break;
        case FastRoute\Dispatcher::FOUND:

            $handler = $routeInfo[1]; // This is the controller and method to call
            $routerParams = $routeInfo[2]; // This is an associative array of parameters extracted from the URI

            // Split the handler into controller class and method
            list($controllerClass, $method) = explode('@', $handler);

            // Check if the controller class exists
            if (!class_exists($controllerClass)) {
                throw new RuntimeException("Controller class $controllerClass not found.");
            }

            // Instantiate the controller with dependencies from the container
            $controlleInstance = $container->get($controllerClass);

            // Read the request body for POST, PUT, DELETE methods
            $requestBody = [];
            if (in_array($httpMethod, ['POST', 'PUT', 'DELETE'])) {
                $requestBody = json_decode(file_get_contents('php://input'), true);
            }

            // Create a Request object
            $request = new Request(
                $httpMethod,
                $uri,
                $routerParams,
                $_GET,
                $requestBody
            );

            // Execute the controller method with the appropriate parameters
            $response = call_user_func_array([$controlleInstance, $method], [$request]);

            $response->send();
            break;
    }
} catch (DomainException $e) {
    error_log("[Index] Domain error: " . $e->getMessage());

    Response::error($e->getStatusCode(), $e->getMessage(), $e->getDetails())->send();
} catch (Throwable $e) {
    error_log("[Index] Bootstrap or Routing error: " . $e->getMessage());

    Response::error(500, "Internal Server Error.")->send();
}
