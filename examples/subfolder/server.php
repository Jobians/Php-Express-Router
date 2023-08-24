<?php

require_once '/path/to/core/Router.php';

$router = new Router();

$router->set('base_path', '/subfolder'); // Set the base path to the subfolder

$router->get('/', function ($req, $res) {
    $res->setHeader('Content-Type', 'text/html');
    $res->send('Hello from the subfolder! <br><a href="/subfolder/nonexistent">Try accessing a non-existent route</a>');
});

$router->use404Error(function ($req, $res, $path) {
    $res->status(404)->send("Oops! The page '$path' was not found in the subfolder.");
});

$router->run();
?>
