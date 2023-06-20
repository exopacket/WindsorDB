<?php

namespace Windsor\Core\Classes\Models;

class WPrimaryKey
{

    private string $key;

    public function __construct()
    {

    }

    public function get()
    {
        return $this->key;
    }

    public static function fromTableColumn(string $column)
    {

    }

    public static function fromPath(string $path)
    {

    }

    public static function default()
    {
        return new WPrimaryKey();
    }

}