<?php

namespace Windsor\Examples;

use Windsor\Core\Classes\XQLModel;

class Results extends \Windsor\Core\Classes\Models\Model
{

    protected array $callables = [];
    protected array $hooks = [];

    protected function schema(XQLModel $model)
    {
        // TODO: Implement schema() method.
    }
}