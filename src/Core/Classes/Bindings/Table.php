<?php

namespace Windsor\Core\Classes\Bindings;

use Windsor\Core\Classes\Models\Model;
use Windsor\Core\Classes\WindsorObject;

class Table extends Binding
{
    private string $table;
    protected array $references;
    private Model $instance;

    public function __construct(string $name, string $table, Model $instance, array $references = [])
    {
        $this->table = $table;
        $this->instance = $instance;
        $this->references = $references;
        parent::__construct($name);
    }

    public function getType(): string
    {
        return "table";
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