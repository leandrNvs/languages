<?php

namespace Src\Routing;

final class Redirect
{
    public static function to($path)
    {
        die(
            header('Location: ' . $path)
        );
    }
}
