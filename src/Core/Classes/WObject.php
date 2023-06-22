<?php

namespace Windsor\Core\Classes;

use Windsor\Core\Supporting\Arrayable;
use Windsor\Core\Supporting\BuildsSchemas;
use Windsor\Core\Supporting\InflectsText;

class WObject implements Arrayable {

    use BuildsSchemas, InflectsText;

    protected array $children;
    protected string $name;

    protected string $path;

    protected bool $searchable = false;
    protected bool $multiple = false;
    protected bool $enforced = false;

    public function __construct($name = null)
    {
        $className = $this->className();
        if($className == "WObject" && !isset($name)) throw Exception("Basic WindsorObject must be constructed with a name.");
        if(!isset($name)) {
            $cases = $this->cases();
            if($this->multiple) $this->name = $cases['plural']['snake'];
            else $this->name = $cases['singular']['snake'];
        } else {
            $this->name = $this->snake($className);
        }
        $this->path = $this->name;
    }

    public function name(): string {
        return $this->name;
    }

    public function children(): array {
        return $this->children ?? [];
    }

    public function path(string $parent = null)
    {
        if(isset($parent)) {
            $this->path = $parent . "." . $this->path;
        }
        return $this->path;
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    public function isEnforced(): bool
    {
        return $this->enforced;
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    public function get(string $path, string $cast = null)
    {
        $path = preg_replace("/[\\/\\\\]/", ".", $path);
        $current = $path;
        if(str_contains($path, ".")) {
            $split = explode(".", $path);
            $next = implode(".", array_splice($split, 1));
            $current = $split[0];
        }

        $value = null;
        if (is_numeric($current)) {
            $current = intval($current);
            if(isset($this->values)) $value = $this->values[$current];
            else if(count($this->children()) > 0) $value = $this->children()[$current];
        } else {
            foreach($this->children() as $child) {
                if($child->name() == $current) {
                    $value = $child;
                    break;
                }
            }
        }

        if(isset($value)) {
            return (isset($next)) ? $value->get($next) : $value;
        } else {
            return null;
        }
    }


    public function json(string $path = null): string
    {
        return "";
    }

    public function xml(string $path = null): string
    {
        return "";
    }

    public function toArray(): array
    {
        $arr = [];
        $arr[$this->name()] = [];
        foreach($this->children() as $child) {
            $arr[$this->name][] = $child->toArray();
        }
        return $arr;
    }

    public function exists(string $path): bool
    {
        return $this->get($path) !== null;
    }

    public function keys(bool $dimensional = true): array
    {
        $arr = [];
        foreach($this->children() as $child) {
            $keys = $child->keys($dimensional);
            if(!$dimensional) array_push($arr, ...array_values($keys));
            else $arr[] = $keys;
        }
        return $arr;
    }

    public function values(bool $dimensional = true): array
    {
        $arr = [];
        foreach($this->children() as $child) {
            $values = $child->values($dimensional);
            if(!$dimensional) array_push($arr, ...array_values($values));
            else $arr[] = $values;
        }
        return $arr;
    }

    public function fromArray(array $arr)
    {
        foreach($arr as $key => $value) {
            if(is_array($value) && is_string($key)) {
                $object = new WindsorObject($key);
                $object->fromArray($value);
                $this->objects[] = $object;
            } else if(is_array($value) && is_numeric($key)) {
                $field = new WindsorField(null, $this->name());
                $field->multiple();
                foreach($value as $val) {
                    $field->appendMultiple($val);
                }
            } else {
                $field = new WindsorField($value, $key);
                $this->objects[] = $field;
            }
        }
    }

    public function first(string $path)
    {
        if (method_exists($this->get($path), 'children')) return $this->get($path)->children()[0];
        else return $this->get($path);
    }

    public function isArray(string $path): bool
    {
        return method_exists($this->get($path), 'children') && $this->get($path)->children() > 1;
    }
}