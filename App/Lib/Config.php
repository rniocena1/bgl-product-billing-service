<?php namespace App\Lib;

class Config
{
    private static $config;

    public static function get($key, $default = null)
    {
        if (is_null(self::$config)) {
            self::$config = require_once(__DIR__.'/../../config.php');
        }

        // Check if config key exist
        // then return using the key provider, otherwise, return the default value
        return !empty(self::$config[$key]) ? self::$config[$key]: $default;
    }
}