<?php

require_once '../../core/Router.php';

$router = new Router();

// Configuration for Default view engine
$router->set('view_engine', 'default');
$router->set('views', '/templates');

$router->get('/template', function($req, $res) {
    $data = ['title' => 'Expressive Router', 'content' => 'Welcome to our website!'];
    $res->render('default_template', $data);
});

$router->run();
