<?php

namespace Windsor\Core\Classes\Bindings;

use Windsor\Core\Classes\Models\Model;
use Windsor\Core\Classes\WindsorObject;

class Attachment extends Binding
{

    protected string $id;
    protected Model $model;

    public function __construct(Model $model, string $id = null)
    {
        $this->model = $model;
        if(isset($id)) $this->id = $id;
    }

    public function setId(string $id)
    {
        $this->id = $id;
    }

    public function getType(): string
    {
        return "model";
    }

    public function retrieve(): WindsorObject
    {
        // TODO: Implement retrieve() method.
    }

    public function update(): WindsorObject
    {
        // TODO: Implement update() method.
    }
}