<?php

namespace Src\Routing;

final class Routes
{
    /**
     * Route's allowed verbs
     *
     * @var array
     */
    private static $verbs = ['get', 'post', 'put', 'patch', 'delete'];

    /**
     * Registered routes
     *
     * @var array
     */
    private static $routes = [];
    
    /**
     * Receive the calls to the class
     *
     * @param string $name
     * @param array  $arguments
     * @return void
     */
    public static function __callStatic($name, $arguments)
    {
        if(in_array($name, self::$verbs)) {
            self::addRoutes($arguments[0], $name, $arguments[1]);
        }
    }

    /**
     * Register the routes
     *
     * @param string  $uri;
     * @param string  $method;
     * @param Closure $closure
     * @return void
     */
    public static function addRoutes($uri, $method, $closure)
    {
        $uri = self::getParameters($uri);

        self::$routes[$uri][$method] = $closure;
    }

    /**
     * Filter and get the route's parameters
     *
     * @param string $uri
     * @return array
     */
    private static function getParameters($uri)
    {
        preg_match_all('/\{(.*?)\}/', $uri, $matches);

        $uri = str_replace($matches[0], '(.*?)', $uri);
        $uri = str_replace('/', '\/', $uri);

        return $uri;
    }

    public static function getRoutes()
    {
        return self::$routes;
    }
}
