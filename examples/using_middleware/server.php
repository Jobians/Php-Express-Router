<?php

require_once '../../core/Router.php';

$router = new Router();

// Custom middleware function
$customMiddleware = function ($req, $res, $next) {
    $res->setHeader('X-Custom-Middleware', 'Applied');
    $next(); // Call the next middleware or route handler
};

// Middleware to log requests
$loggerMiddleware = function ($req, $res, $next) {
    $timestamp = date('Y-m-d H:i:s');
    $method = $req->method;
    $path = $req->route;
    file_put_contents('requests.log', "$timestamp - $method $path\n", FILE_APPEND);
    $next(); // Call the next middleware or route handler
};

$router->use($loggerMiddleware); // Apply logger middleware to all routes

$router->get('/', function ($req, $res) {
    $res->send('Hello from middleware example!');
});

$router->get('/protected', $customMiddleware, function ($req, $res) {
    $res->send('This route is protected by custom middleware.');
});

$router->run();
