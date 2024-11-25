<?php

namespace Src\Storage;

final class Storage
{
    private static $path;

    public static function path($path)
    {
        self::$path = $path . 'storage.txt';

        if(!file_exists($path)) mkdir($path);

        if(!file_exists(self::$path)) {
            self::initialize();
        }
    }

    public static function store($data)
    {
        file_put_contents(self::$path, json_encode($data));
    }

    public static function read()
    {
        return json_decode(file_get_contents(self::$path), true);
    }

    private static function initialize()
    {
        $data = [];

        $data['last-added'] = [];
        $data['titles'] = [];
        $data['notes'] = [];

        self::store($data);
    }
}
