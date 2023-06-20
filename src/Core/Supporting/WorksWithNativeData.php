<?php

namespace App\Windsor\Core\Supporting;

use Windsor\Core\Types\WindsorDataType;
use Windsor\Core\Types\WindsorDataTypeCollection;

trait WorksWithNativeData
{
    protected function cast(string|WindsorDataType $type, string $value = null)
    {
        $type = is_string($type) ? WindsorDataTypeCollection::fromStr($type) : $type;
        switch($type) {
            case WindsorDataType::INTEGER:
                return intval($value);
            case WindsorDataType::FLOAT:
                return floatval($value);
        }
    }
}