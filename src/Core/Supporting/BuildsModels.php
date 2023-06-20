<?php

namespace Windsor\Core\Supporting;

use Windsor\Core\Classes\Models\WPrimaryKey;

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

    protected function primary(WPrimaryKey $key) : void
    {
        $this->primary = $key;
    }

}