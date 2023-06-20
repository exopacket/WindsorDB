<?php

namespace Windsor\Core\Classes\Bindings;

use Windsor\Core\Classes\WObject;

abstract class WBinding
{

    protected array $references = [];

    public abstract function getType(): string;
    public abstract function retrieve(): WObject;
    public abstract function update(): WObject;

    public function store(): void
    {
        $type = $this->getType();
        $refs = json_encode($this->references);
    }

}