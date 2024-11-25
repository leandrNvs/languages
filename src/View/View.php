<?php

namespace Src\View;

final class View
{

    private static $path;

    public static function path($path)
    {
        self::$path = $path;
    }

    public static function render($view, $data = [])
    {
        $file = file_get_contents(self::$path . $view . '.html');

        return self::parseData($file, $data);
    }

    private static function parseData($view, $data)
    {
        $keys = array_map(function($i) {
            return "{{{$i}}}";
        }, array_keys($data));

        return str_replace($keys, array_values($data), $view);
    }
}
