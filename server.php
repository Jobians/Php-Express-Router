<?php

require_once 'core/Router.php';

$router = new Router();

$router->get('/', function($req, $res) {
    $res->send('Hello, World!');
});

$router->run();
