<?php
require_once '/path/to/core/Router.php';

$router = new Router();

// Set the static directory to '/public'
$router->set('static', '/public');

// Set the view engine to 'smarty'
$router->set('view_engine', 'smarty');

// Set the directory for view templates
$router->set('views', '/views');


// Define a route that renders a template
$router->get('/render', function ($req, $res) {
    // Set data to be passed to the template
    $data = [
        'title' => 'Render Example',
        'content' => 'This is the content of the rendered template.',
    ];

    // Render the template using the Smarty view engine
    $res->render('template', $data);
});

$router->run();
?>
