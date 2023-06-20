<?php

namespace Windsor\Core\Classes\Bindings;

use Windsor\Core\Classes\Models\WModel;

class WAttachment extends WBinding
{

    protected string $id;
    protected WModel $model;

    public function getType(): string
    {
        return "model";
    }
}