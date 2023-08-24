# Php Express Router

Php Express Router is a lightweight and flexible PHP routing library that allows you to easily handle HTTP requests and define routes for your web application.

## Features

- Define routes for various HTTP methods like GET, POST, PUT, DELETE, PATCH, OPTIONS, and more.
- Attach middleware to routes to perform actions before or after route handling.
- Handle static files using the provided StaticMiddleware.
- Set global shared data accessible throughout your application.
- Configure error handlers for handling HTTP errors.
- And more...

## Installation

To get started using Php Express Router, follow these steps:

1. Clone the repository or download the source code.

   ```
   git clone https://github.com/Jobians/Php-Express-Router.git
   ```

2. Place the cloned files in your project directory:

   ```
   your_project/
       - core/
           - modules/
               - smarty/
           - middlewares/
               - static.php
           - Request.php
           - Response.php
           - Router.php
       server.php
       .htaccess
   ```

   The `core/` directory contains essential router files and modules. The `middlewares/` directory holds the built-in static middleware file.

## Usage

Here are some usage examples to get you started:

### Basic Route

```php
require_once 'core/Router.php';

$router = new Router();

$router->get('/', function($req, $res) {
    $res->send('Hello, World!');
});

$router->run();
```

### Configuration Setup

You can configure the router using the `set` method. This method allows you to customize various aspects of the router's behavior. Here are the available configuration options:

- **base_path**: Set the base URL path for your application. This can be useful if your application is not hosted at the root of the domain.
  ```php
  $router->set('base_path', '/your-project');
  ```

- **view_engine**: Choose the view engine to use for rendering templates. You can choose between `'default'` (native PHP) and `'smarty'`.
  ```php
  $router->set('view_engine', 'smarty');
  ```

- **views**: Specify the directory where your view templates are located.
  ```php
  $router->set('views', '/views');
  ```

- **template_cache_dir**: Set the directory for Smarty template caching, applicable only if using Smarty view engine.
  ```php
  $router->set('template_cache_dir', '/cache');
  ```

- **template_caching**: Enable or disable template caching for Smarty.
  ```php
  $router->set('template_caching', true);
  ```

- **static**: Set the base directory for serving static files. You can use this to specify a public directory for assets like CSS, JavaScript, and images.
  ```php
  $router->set('static', '/public');
  ```

It's important to set these configurations before defining routes and starting the router using the `run` method. Configuration settings will affect the router's behavior and how it handles requests.

### More Examples

For additional usage examples, check the [examples folder](examples) in this repository. You'll find more scenarios and use cases that can help you get a deeper understanding of how to use Php Express Router effectively.

### Middleware

```php
$router->use(function($req, $res, $next) {
    // Middleware logic
    $next();
});

$router->get('/protected', function($req, $res) {
    $res->send('This route is protected by middleware.');
});
```

### 404 Error Handling

```php
$router->use404Error(function($req, $res, $path) {
    $res->status(404)->send("Route not found: $path");
});
```

...

## Contributing

Contributions are welcome! If you find any issues or want to add new features, feel free to submit a pull request.

## License

This project is licensed under the [MIT License](LICENSE).
