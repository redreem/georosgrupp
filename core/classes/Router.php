<?php

class Router
{

    public static $route = [];

    public static function routing()
    {
        $request_uri = trim($_SERVER['REQUEST_URI'], '/');

        $uri_params_index = strpos($request_uri, '?');

        if ($uri_params_index) {

            $route = substr($request_uri, 0, $uri_params_index);
        }
        self::$route = explode('/', (!isset($route) ? $request_uri : $route));

        if (empty(self::$route[0])) {

            self::$route[0] = Core::$config['default_controller'];
        }
    }

}