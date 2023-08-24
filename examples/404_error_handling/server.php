<?php

require_once '../../core/Router.php';

$router = new Router();

// Custom error handler for 404 errors
$notFoundHandler = function ($req, $res, $path) {
    $res->status(404)->send("Oops! The page '$path' was not found.");
};

// Apply the 404 error handler
$router->use404Error($notFoundHandler);

$router->get('/', function ($req, $res) {
    $res->setHeader('Content-Type', 'text/html');
    $res->send('Hello from error handling example! <br><a href="/nonexistent">Try accessing a non-existent route</a>');
});

$router->run();
