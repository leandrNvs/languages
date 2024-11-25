<?php

namespace Src\Http;

final class Request
{
    private $input = [];

    private $currentRequestUri;

    private $currentRequestMethod;

    private static $instance;

    public function __construct()
    {
        $this->setCurrentRequestMethod();
        $this->setCurrentRequestUri();

        $this->input = $_POST;
    }
    
    public static function capture()
    {
        return new static;
    }

    public static function setInstance(Request $request)
    {
        self::$instance = $request;
    }

    private function setCurrentRequestMethod()
    {
        $this->currentRequestMethod = strtolower($_POST['_method'] ?? $_SERVER['REQUEST_METHOD']);

        unset($_POST['_method']);
    }

    public function input($field, $default = null)
    {
        return $this->input[$field] ?? $default;
    }

    private function setCurrentRequestUri()
    {
        $this->currentRequestUri = $_SERVER['REQUEST_URI'];
    }

    public function getCurrentRequestUri()
    {
        return $this->currentRequestUri;
    }

    public function getCurrentRequestMethod()
    {
        return $this->currentRequestMethod;
    }

    public static function getInstance()
    {
        return self::$instance;
    }
}
