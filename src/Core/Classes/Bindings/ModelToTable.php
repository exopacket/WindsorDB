<?php

namespace Windsor\Core\Classes\Bindings;

use Windsor\Core\Classes\Models\Model;
use Windsor\Core\Classes\WindsorObject;

class ModelToTable extends Binding
{

    private Model $instance;
    protected array $references;
    protected string $table;

    public function __construct(string $name, string $table, array $references, Model $instance)
    {
        $this->references = $references;
        $this->instance = $instance;
        parent::__construct($name);
    }

    public function getType(): string
    {
        return "model_to_table";
    }

    public function retrieve(): WindsorObject
    {
        $values = [];
        foreach($this->references as $key => $value) {
            $val = $this->instance->get($value);
            $values[] = [ $key => $val ];
        }
        //TODO update db
        return $values;
    }

    public function update(): WindsorObject
    {
        $values = [];
        foreach($this->references as $key => $value) {
            $val = $this->instance->get($value);
            $values[] = [ $key => $val ];
        }
        //TODO update db
        return $values;
    }
}