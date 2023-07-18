<?php

namespace Windsor\Core\Classes\Bindings;

use Windsor\Core\Classes\Models\Model;

class Attachment extends Binding
{

    protected string $id;
    protected Model $model;

    public function getType(): string
    {
        return "model";
    }
}