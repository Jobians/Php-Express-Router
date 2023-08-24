<?php

require_once '../../core/Router.php';

$router = new Router();

// Define routes for various HTTP methods
$router->get('/', function ($req, $res) {
    $res->send('Welcome to our website! (GET)');
});

$router->post('/', function ($req, $res) {
    $res->send('Data submitted! (POST)');
});

$router->put('/', function ($req, $res) {
    $res->send('Data updated! (PUT)');
});

$router->delete('/', function ($req, $res) {
    $res->send('Data deleted! (DELETE)');
});

$router->patch('/', function ($req, $res) {
    $res->send('Data patched! (PATCH)');
});

$router->options('/', function ($req, $res) {
    $res->send('Options requested! (OPTIONS)');
});

// Define a fallback route for all HTTP methods
$router->all('/fallback', function ($req, $res) {
    $method = $req->method;
    $res->send("Fallback route for $method requests");
});

// Define a route with a parameter
$router->get('/users/:id', function ($req, $res) {
    $userId = $req->params['id'];
    $res->send("User with ID $userId requested");
});

// Route with a parameter for a specific user
$router->get('/users/:username', function ($req, $res) {
    $username = $req->params['username'];
    $res->send("Profile of user: $username");
});

// Route with multiple parameters
$router->get('/products/:category/:product_id', function ($req, $res) {
    $category = $req->params['category'];
    $productId = $req->params['product_id'];
    $res->send("Product details - Category: $category, Product ID: $productId");
});

// Route with a wildcard for capturing the rest of the URL
$router->get('/articles/*', function ($req, $res) {
    $path = $req->params[0];
    $res->send("Requested article path: $path");
});

// Route with a wildcard and a parameter
$router->get('/categories/:category/*', function ($req, $res) {
    $category = $req->params['category'];
    $path = $req->params[0];
    $res->send("Requested category: $category, Article path: $path");
});

$router->run();
?>
