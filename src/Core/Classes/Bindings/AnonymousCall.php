<?php

namespace Windsor\Core\Classes\Bindings;

use Closure;
use Windsor\Core\Classes\Models\Model;
use Windsor\Core\Classes\WindsorObject;

class AnonymousCall extends Binding
{

    private Model $instance;
    private Closure $fn;
    private array $args;

    public function __construct(Model $model, string $name, array $args, Closure $fn)
    {
        $this->instance = $model;
        $this->fn = $fn;
        $this->args = $args;
        parent::__construct($name);
    }

    public function getType(): string
    {
        return "anonymous_call";
    }

    public function retrieve(): WindsorObject
    {
        array_unshift($this->args, $this->instance);
        return $this->fn(...$this->args);
    }

    public function update(): WindsorObject
    {
        array_unshift($this->args, $this->instance);
        return $this->fn(...$this->args);
    }
}