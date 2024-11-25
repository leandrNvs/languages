<?php

namespace Src\Http;

use ReflectionFunction;
use Src\Routing\Routes;

final class Kernel
{
    public static function send(Request $request)
    {
        Request::setInstance($request);

        $routes = Routes::getRoutes();

        $uri = $request->getCurrentRequestUri();
        $method = $request->getCurrentRequestMethod();

        foreach($routes as $pattern => $route) {
            if(preg_match("/^$pattern$/", $uri, $matches)) {
                unset($matches[0]);

                return self::execute($route[$method], $matches);
            }
        }
    }

    private static function execute($closure, $parameters)
    {
        $reflection = new ReflectionFunction($closure);

        $refParameters = $reflection->getParameters();

        if(isset($refParameters[0]) && $refParameters[0]->getType()?->getName() === Request::class) {
            array_unshift($parameters, Request::getInstance());
        }

        return $reflection->invokeArgs($parameters);
    }
}
