<?php

/*
 * Response Class
 *
 * This is Php Express Router Response class. The Response class is used to create the Response Object
 * which is used to handle/process user's response. Request class contains methods and properties
 * that are used to process a user's response.
 *
 * @copyright 2023 Copyright (c) JOBIANS TECHIE <jobianstechie@gmail.com>
 * @license MIT
 */
 
class Response
{
    public function send($content, $http_headers = []) {
        if (is_array($http_headers) && !empty($http_headers)) {
          foreach ($http_headers as $key => $value) {
              header($key . ': ' . $value);
          }
      }
      echo $content;
    }

    public function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function redirect($url)
    {
        header("Location: $url");
        exit();
    }
    
    protected function getConfig($name)
    {
        return Router::getConfig($name);
    }
    
    public function render($template, $data=[])
    {
        if ($this->getConfig('view_engine') === null) {
            trigger_error("View engine is not specified.", E_USER_WARNING);
            return;
        }
        
        foreach (Router::$sharedData as $key => $value) {
            $data[$key] = $value;
        }
        
        $viewsDirectory = $_SERVER['DOCUMENT_ROOT'] . $this->getConfig('base_path', '/') . $this->getConfig('views');

        switch (strtolower($this->getConfig('view_engine', 'default'))) {
            case 'default':
                $viewPath = $viewsDirectory . '/' . $template;
                if (pathinfo($viewPath, PATHINFO_EXTENSION) !== 'php') {
                    $viewPath .= '.php';
                }
                break;

            case 'smarty':
                $smartyFilePath = __DIR__ . '/modules/smarty/Smarty.class.php';
                if (!file_exists($smartyFilePath)) {
                    trigger_error("Smarty module class file not found: $smartyFilePath", E_USER_WARNING);
                    return;
                }
                require $smartyFilePath;
                $smarty = new Smarty;

                // Configure Smarty
                $smarty->template_dir = $viewsDirectory;

                // Enable caching if set
                if ($this->getConfig('template_caching')) {
                    $smarty->cache_dir = $_SERVER['DOCUMENT_ROOT'] . $this->getConfig('base_path', '/') . $this->getConfig('template_cache_dir');
                    $smarty->caching = true;
                }

                // Assign values
                foreach ($data as $key => $value) {
                    $smarty->assign($key, $value);
                }

                // Load the view
                $smartyTemplatePath = $template;
                if (pathinfo($smartyTemplatePath, PATHINFO_EXTENSION) !== 'tpl') {
                    $smartyTemplatePath .= '.tpl';
                }
                if (file_exists($viewsDirectory . '/' . $smartyTemplatePath)) {
                    $smarty->display($smartyTemplatePath);
                } else {
                    trigger_error("Smarty module template file not found: ". $viewsDirectory . '/' . $smartyTemplatePath, E_USER_WARNING);
                }
                return;

            default:
                trigger_error("Unsupported view engine: {$this->getConfig('view_engine')}", E_USER_WARNING);
                return;
        }

        if (file_exists($viewPath)) {
            extract($data);
            ob_start();
            include $viewPath;
            $content = ob_get_clean();
            $this->send($content);
        } else {
            trigger_error("View file not found: $viewPath", E_USER_WARNING);
        }
    }
    
    public function setHeader($name, $value) {
    	header($name.':'.$value);
    }

    public function setSession($name, $value)
    {
        session_start();
        $_SESSION[$name] = $value;
    }

    public function setCookie($name, $value, $expires = 0, $path = '', $domain = '', $secure = false, $httponly = false)
    {
        setcookie($name, $value, $expires, $path, $domain, $secure, $httponly);
    }

    public function status($statusCode)
    {
        http_response_code($statusCode);
        return $this;
    }

    public function sendStatus($statusCode)
    {
        http_response_code($statusCode);
        $statusText = $this->getStatusCodeText($statusCode);
        $this->send("$statusCode $statusText");
    }

    protected function getStatusCodeText($statusCode)
    {
        $statusTexts = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            103 => 'Early Hints',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            208 => 'Already Reported',
            226 => 'IM Used',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Payload Too Large',
            414 => 'URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => "I'm a teapot",
            421 => 'Misdirected Request',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Too Early',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            451 => 'Unavailable For Legal Reasons',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            508 => 'Loop Detected',
            510 => 'Not Extended',
            511 => 'Network Authentication Required',
        ];

        return isset($statusTexts[$statusCode]) ? $statusTexts[$statusCode] : 'Unknown Status Code';
    }
}
