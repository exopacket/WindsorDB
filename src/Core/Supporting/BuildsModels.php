<?php

namespace Windsor\Core\Supporting;

use Windsor\Core\Classes\Bindings\Attachment;
use Windsor\Core\Classes\Models\PrimaryKey;
use Windsor\Core\Classes\WindsorObject;

trait BuildsModels
{

    protected function static() : void
    {
        $this->static = true;
    }

    protected function final(): void
    {
        $this->final = true;
    }

    protected function primary(PrimaryKey $key) : void
    {
        $this->primary = $key;
    }

    protected function attach(string $model, string $id = null)
    {
        $instance = new $model();
        $attachment = new Attachment($instance, $id);
        $this->children[] = $attachment;
        return $attachment;
    }

    protected function group(string $name)
    {
        $object = new WindsorObject($name);
        $this->children[] = $object;
        return $object;
    }

}