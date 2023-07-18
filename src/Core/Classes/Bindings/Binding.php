<?php

namespace Windsor\Core\Classes\Bindings;

use Windsor\Core\Classes\WindsorObject;

abstract class Binding
{

    protected array $references = [];

    public abstract function getType(): string;
    public abstract function retrieve(): WindsorObject;
    public abstract function update(): WindsorObject;

    public function store(): void
    {
        $type = $this->getType();
        $refs = json_encode($this->references);
    }

}