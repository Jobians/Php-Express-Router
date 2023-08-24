<?php

/*
 * StaticMiddleware Class
 *
 * This is Php Express Router StaticMiddleware class.
 * @copyright 2023 Copyright (c) JOBIANS TECHIE <jobianstechie@gmail.com>
 * @license MIT
 */
 
class StaticMiddleware
{
    protected $publicPath;
    protected $mime_types;

    public function __construct($publicPath)
    {
        $this->publicPath = $publicPath;
        $this->mime_types = array(
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
        );
    }

    public function __invoke($req, $res, $next)
    {
        $path = $this->publicPath;
        
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        if (file_exists($path) && is_file($path)) {
            if (isset($this->mime_types[$extension])) {
                header('Content-Type: ' . $this->mime_types[$extension]);
                readfile($path);
                exit;
            } else {
                $res->status(415)->send("File type not supported.");
                exit;
            }
        }
        
        $next();
    }
}
