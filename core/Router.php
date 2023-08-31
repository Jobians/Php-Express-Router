<?php

require_once 'Request.php';
require_once 'Response.php';
require_once 'middlewares/static.php';

class Router
{
    protected $routes = [];
    protected $middleware = [];
    protected $errorHandlers = [];
    protected static $config = [];
    public static $sharedData = [];
    
    
    public function setGlobal($key, $value)
    {
        self::$sharedData[$key] = $value;
    }

    public function getGlobal($key, $default = null)
    {
        return self::$sharedData[$key] ?? $default;
    }
    
    public static function set($key, $value)
    {
        self::$config[$key] = $value;
    }

    public static function getConfig($key, $default = null)
    {
        return self::$config[$key] ?? $default;
    }

    public function get($path, ...$handlers)
    {
        $this->addRoute('GET', $path, $handlers);
    }
    
    public function post($path, ...$handlers)
    {
        $this->addRoute('POST', $path, $handlers);
    }

    public function put($path, ...$handlers)
    {
        $this->addRoute('PUT', $path, $handlers);
    }

    public function delete($path, ...$handlers)
    {
        $this->addRoute('DELETE', $path, $handlers);
    }

    public function patch($path, ...$handlers)
    {
        $this->addRoute('PATCH', $path, $handlers);
    }

    public function options($path, ...$handlers)
    {
        $this->addRoute('OPTIONS', $path, $handlers);
    }
    
    public function all($path, ...$handlers)
    {
        $this->addRoute('ALL', $path, $handlers);
    }

    public function use($middleware)
    {
        $this->middleware[] = $middleware;
    }
    
    protected function addRoute($method, $path, $handlers)
    {
        $this->routes[$method][$path] = $handlers;
    }
    
    public function use404Error($handler)
    {
        $this->errorHandlers[404] = $handler;
    }
    
    public function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $req = new Request();
        $res = new Response();
        
        $path = $req->getPath();
        $basePath = $this->getConfig('base_path', '');
        
        if (!empty($basePath) && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
          }

        $staticDirectory = $this->getConfig('static');
        if ($staticDirectory !== null) {
            $staticPath = $_SERVER['DOCUMENT_ROOT'] . $basePath . $staticDirectory . $path;
            $staticMiddleware = new StaticMiddleware($staticPath);
            $staticMiddleware($req, $res, function () use ($method, $path, $req, $res) {
                $this->handleRoute($method, $path, $req, $res);
            });
        } else {
            $this->handleRoute($method, $path, $req, $res);
        }
    }

    private function handleRoute($method, $path, $req, $res) {
        foreach (['ALL', $method] as $currentMethod) {
            if (isset($this->routes[$currentMethod])) {
                foreach ($this->routes[$currentMethod] as $route => $handlers) {
                    if ($this->matchRoute($route, $path, $req)) {
                        $handlers = array_merge($this->middleware, $handlers);
                        $this->processHandlers($req, $res, $handlers);
                        return;
                    }
                }
            }
        }

        // Handle 404
        if (isset($this->errorHandlers[404])) {
            $this->executeErrorHandler(404, $req, $res, $path);
            return;
        } else {
            (new Response())->status(404)->send("Cannot $method $path");
            return;
        }
    }

    private function processHandlers($req, $res, $handlers)
    {
        $next = null;

        $next = function () use (&$handlers, $req, $res, &$next) {
            $handler = array_shift($handlers);

            if ($handler) {
                $handler($req, $res, $next);
            }
        };

        $next();
    }
    
    protected function matchRoute($route, $path, $req) {
        $routeParts = explode('/', trim($route, '/'));
        $pathParts = explode('/', trim($path, '/'));

        $numParts = min(count($routeParts), count($pathParts));

        for ($i = 0; $i < $numParts; $i++) {
            if ($routeParts[$i] === '*') {
                if (isset($pathParts[$i])) {
                    $req->params[0] = implode('/', array_slice($pathParts, $i));
                    return true;
                } else {
                    return false;
                }
            }
            if (strpos($routeParts[$i], ':') === 0) {
                $req->setParam(substr($routeParts[$i], 1), $pathParts[$i]);
                continue;
            }
            if ($routeParts[$i] !== $pathParts[$i]) {
                return false;
            }
        }
    
        return count($routeParts) === count($pathParts);
    }
    
    private function executeErrorHandler($statusCode, $req, $res, $path = null) {
        $errorHandler = $this->errorHandlers[$statusCode];
        $errorHandler($req, $res, $path);
    }
}