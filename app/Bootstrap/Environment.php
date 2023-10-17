<?php

namespace App\Bootstrap;

class Environment
{
    private static $constants = [];
    private static $env = [];

    public function __construct()
    {
        ($myfile = fopen(dirname(dirname(__DIR__)) . '/configuration.env', 'r')) or die('Unable to open file!');
        $data = fread($myfile, filesize(dirname(dirname(__DIR__)) . '/configuration.env'));
        self::$env = explode("\n", $data);

        foreach (self::$env as $value) {
            self::$constants[trim(explode('=', $value)[0])] = trim(
                explode('=', $value)[1]
            );
        }
    }
    public static function env($key)
    {
        return self::$constants[$key];
    }
}
