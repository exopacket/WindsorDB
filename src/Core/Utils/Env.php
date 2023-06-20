<?php

namespace Windsor\Core\Utils;

use Dotenv\Dotenv;

class Env
{

    private static Env $global;

    public function __construct(string $filename = null)
    {
        $root = str_replace("/public", "", realpath("."));
        $env = (isset($filename)) ? Dotenv::createImmutable($root, $filename) : Dotenv::createImmutable($root);
        //$env->required([]);
        $env->load();
    }

    public static function get(string $key)
    {
        if(!isset(self::$global)) self::$global = new self(".env.Windsor");
        return $_ENV[$key];
    }

}