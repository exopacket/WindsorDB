<?php

namespace Windsor\Examples;

use Windsor\Core\Classes\Models\Model;

class Results extends \Windsor\Core\Classes\Models\Model
{

    protected array $callables = [
        "add" => self::class
    ];

    protected array $hooks = [];

    protected array $casts = [];

    protected function schema(Model $model)
    {
        $model->field("test");
    }

    protected static function add() {

    }

}