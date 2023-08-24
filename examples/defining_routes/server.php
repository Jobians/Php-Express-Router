<?php

require_once '../../core/Router.php';

$router = new Router();

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

$router->all('/fallback', function ($req, $res) {
    $method = $req->method;
    $res->send("Fallback route for $method requests");
});

$router->run();
