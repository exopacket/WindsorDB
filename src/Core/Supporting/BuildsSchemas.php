<?php

namespace Windsor\Core\Supporting;

use Windsor\Core\Classes\Bindings\AnonymousCall;
use Windsor\Core\Classes\Bindings\Binding;
use Windsor\Core\Classes\Bindings\ModelToTable;
use Windsor\Core\Classes\Bindings\Table;
use Windsor\Core\Classes\Field;
use Windsor\Core\Classes\WindsorObject;

trait BuildsSchemas
{

    protected function tableByFields(string $from, string|array $references, string $name = null): Binding
    {
        $refs = is_string($references) ? [$references] : $references;
        $binding = new Table($name ?? $from, $from, $this, $refs);
        $this->objects[] = $binding;
        return $binding;
    }

    protected function table(string $from, string $name = null): Binding
    {
        $binding = new Table($name ?? $from, $from, $this);
        $this->objects[] = $binding;
        return $binding;
    }

    protected function modelToTable(string $table, array $references, string $name = null): Binding
    {
        $binding = new ModelToTable($name ?? $table, $table, $references, $this);
        $this->objects[] = $binding;
        return $binding;
    }

    protected function field(string $name): Field
    {
        $object = new Field(null, $name);
        $this->objects[] = $object;
        return $object;
    }

    protected function dynamic($name, array $params, $fn): Binding
    {
        $call = new AnonymousCall($this, $name, $params, $fn);
        $this->objects[] = $call;
        return $call;
    }

    protected function defined(string $name, string $classpath, string $fn): void
    {
        $this->callables[] = [ $name => [ $classpath, $fn, ] ];
    }

    protected function branch(string $name): WindsorObject
    {
        $object = new WindsorObject($name);
        $this->objects[] = $object;
        return $object;
    }

    protected function searchable()
    {
        $this->searchable = true;
        return $this;
    }

    protected function multiple() {
        $this->multiple = true;
        return $this;
    }

    protected function enforced() {
        $this->enforced = true;
        return $this;
    }


}