<?php

require_once '../../core/Router.php';

$router = new Router();

// Configuration for Smarty view engine
$router->set('view_engine', 'smarty');
$router->set('views', '/templates');

$router->get('/template', function($req, $res) {
    $data = ['title' => 'Expressive Router', 'content' => 'Welcome to our website!'];
    $res->render('template', $data);
});

$router->run();
