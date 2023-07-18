<?php

namespace Windsor\Core\Classes;

use XQL\Core\Types\WType;
use XQL\Core\Types\XQLtype;

class Field
{

    protected WType $type;
    protected $value;

    public function __construct($value = null, $name = null, WType $type = null)
    {
        if(isset($value)) $this->value = $value;
        if(isset($type)) $this->type = $type;
        else $this->type = WType::DYNAMIC;
        parent::__construct($name);
    }

    public function value($value = null) {
        if(isset($value)) $this->value = $value;
        if($this->multiple && isset($this->values) && count($this->values) > 0) return $this->values;
        return $this->value;
    }

    public function type($type = null) {
        if(isset($type)) $this->type = $type;
        return $this->type;
    }

    public function keys(bool $dimensional = true): array
    {
        $res = [];
        if($this->multiple && isset($this->values) && count($this->values) > 0) {
            if(!$dimensional) return [$this->groupName(), $this->fieldName()];
            $duplicates = [];
            foreach($this->values as $value) {
                $duplicates[] = $this->fieldName();
            }
            $res[$this->groupName()] = $duplicates;
        } else {
            $res[] = $this->fieldName();
        }

        return $res;
    }

    public function values(bool $dimensional = true): array
    {
        return ($this->multiple && isset($this->values) && count($this->values) > 0) ? $this->values : [$this->value];
    }

}