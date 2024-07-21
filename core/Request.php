<?php

/*
 * Request Class
 *
 * This is Php Express Router Request class. The Request class is used to create the Request Object
 * which is used to handle/process user's request. Request class contains methods and properties
 * that are used to process a user's request.
 *
 * @copyright 2023 Copyright (c) JOBIANS TECHIE <jobianstechie@gmail.com>
 * @license MIT
 */

class Request
{
    public $params = [];

    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
    }

    public function getPath()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getBody()
    {
        return file_get_contents('php://input');
    }



    /**
     *  Check if at least one file upload is present in the request
     *  @param string $filename Name of the file upload to check
     *  @return bool 
     */


    public function hasFile($filename)
    {

        if (isset($_FILES[$filename]) and  is_array(
            $_FILES[$filename]['name']
        )) {
            return !empty($_FILES[$filename]['name'][0]);
        }

        return  !empty($_FILES[$filename]['name']);
    }



    public function files($filename)
    {
        // import file upload class
        include "modules/Utils/FileUpload.php";
        $FileUpload = new FileUpload(
            $_FILES[$filename],
            is_array($_FILES[$filename]['name'])
        );

        return $FileUpload;
    }




    public function getHeaders()
    {
        return getallheaders();
    }

    public function getHeader($name)
    {
        $headers = $this->getHeaders();
        return isset($headers[$name]) ? $headers[$name] : null;
    }

    public function isSecure()
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] == 443);
    }

    public function getBaseUrl()
    {
        $protocol = $this->isSecure() ? 'https' : 'http';
        $host = $this->getHeader('Host');
        return "$protocol://$host";
    }

    public function getFullUrl()
    {
        return $this->getBaseUrl() . $_SERVER['REQUEST_URI'];
    }

    public function accepts($type)
    {
        $acceptHeader = $this->getHeader('Accept');
        $acceptedTypes = explode(',', $acceptHeader);
        return in_array($type, $acceptedTypes);
    }

    public function acceptsLanguage($language)
    {
        $acceptLanguageHeader = $this->getHeader('Accept-Language');
        $acceptedLanguages = explode(',', $acceptLanguageHeader);
        return in_array($language, $acceptedLanguages);
    }

    public function acceptsCharset($charset)
    {
        $acceptCharsetHeader = $this->getHeader('Accept-Charset');
        $acceptedCharsets = explode(',', $acceptCharsetHeader);
        return in_array($charset, $acceptedCharsets);
    }

    public function __get($name)
    {
        switch ($name) {
            case 'query':
                return $_GET;
            case 'post':
                return $_POST;
            case 'body':
                $requestBody = file_get_contents('php://input');
                return json_decode($requestBody, true, 512, JSON_PARTIAL_OUTPUT_ON_ERROR);
            case 'headers':
                return $this->getHeaders();
            case 'cookies':
                return $_COOKIE;
            case 'route':
                return $_SERVER['REQUEST_URI'];
            case 'session':
                if (session_status() === PHP_SESSION_NONE) {
                  session_start();
                }
                return $_SESSION;
            case 'method':
                return $_SERVER['REQUEST_METHOD'];
            case 'isSecure':
                return $this->isSecure();
            case 'isAjax':
                return $this->isAjax();
            case 'isJson':
                return $this->isJson();
            case 'ip':
                return $_SERVER['REMOTE_ADDR'];
            case 'baseUrl':
                return $this->getBaseUrl();
            case 'fullUrl':
                return $this->getFullUrl();
            case 'referer':
                return $this->getHeader('Referer');
            case 'userAgent':
                return $this->getHeader('User-Agent');
            case 'accepts':
                return $this->getHeader('Accept');
            case 'acceptsLanguage':
                return $this->getHeader('Accept-Language');
            case 'acceptCharset':
                return $this->getHeader('Accept-Charset');
            default:
                return 'Class property ' . $name . ' not declared';
        }
    }
}
