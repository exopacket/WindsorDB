<?php

namespace Windsor\Core\Supporting\Cloud;

class Cloud
{
    
    private static string $driver = "s3";
    private static S3 $aws;

    public static function put(string $key, string $content) {
        self::driver()->put($key, $content);
    }

    public static function get(string $key)
    {
        return self::driver()->get($key);
    }

    private static function driver()
    {
        if(!isset(self::$driver)) return null;
        if(self::$driver === 's3') {
            if (!isset(self::$aws)) self::$aws = new S3();
            return self::$aws;
        } else { return null; }
    }
    
}