<?php

namespace Windsor\Core\Supporting;

use Exception;
use Windsor\Core\Classes\Bindings\Binding;
use Windsor\Core\Classes\Bindings\Callable;
use Windsor\Core\Classes\Bindings\WStore;
use Windsor\Core\Classes\Models\Model;
use Windsor\Core\Classes\Field;
use Windsor\Core\Classes\WindsorObject;
use Windsor\Core\Utils\DynamicArr;
use Windsor\DB\DBX;

trait BuildsQueries
{

    public static function fetch(string $id)
    {
        $class = get_called_class();
        $instance = new $class(['id' => $id]);
        return $instance;
    }

    public static function create(array|\Closure $data = null): Model
    {
        $class = get_called_class();
        $instance = new $class();
        $instance->path($instance->modelKey());

        if(is_array($data)) {
            $values = $data;
        } else if(is_callable($data) || is_a($data, 'Closure')) {
            $reflector = new \ReflectionFunction($data);
            $num_params = $reflector->getNumberOfParameters();
            if ($num_params > 1) throw new Exception("Expected at max 1 parameter, got " . $num_params);
            $values = $data($instance);
            if (!is_array($values)) throw new Exception("An array of values must be returned by the callback.");
        } else {
            throw new Exception("The parameters for create was invalid.");
        }

        self::construct($instance, $instance, $values, $instance->modelKey());

        return $instance;
    }

    private static function construct(Model $instance, WindsorObject $object, array $values, string $path) {

        $dArr = new DynamicArr($values);

        $parentIsSearchable = $object->isSearchable();
        if($parentIsSearchable) DBX::updateSearchableFields($instance, $object);

        $i = 0;
        foreach($object->children() as $child) {

            if(is_array($child)) $child = array_values($child)[0];
            $childIsSearchable = $child->isSearchable();
            if(!$parentIsSearchable && $childIsSearchable) DBX::updateSearchableFields($instance, $child);

            $child->path($path);

            if($child instanceof Field) {

                if($dArr->exists($child->name())) {
                    $dKey = $dArr->find($child->name());
                    if(is_array($values[$dKey])) {
                        foreach($values[$dKey] as $value) {
                            $child->insert($value);
                            if($parentIsSearchable || $childIsSearchable) DBX::insertSearchableValue($instance, $child, $value);
                        }
                    } else {
                        $child->value($values[$dKey]);
                        if($parentIsSearchable || $childIsSearchable) DBX::insertSearchableValue($instance, $child);
                    }
                } else if($child->isEnforced()) {
                    throw new Exception($child->name() . " is required.");
                }

            } else if($child instanceof Binding) {

                if ($dArr->exists($child->name())) { //IF VALUES EXIST

                    $dKey = $dArr->find($child->name());
                    $child->retrieve($instance, $child, $values[$dKey]);
                    self::construct($instance, $child, $values[$dKey], $child->path());

                } else if($child instanceof Callable) { //NO VALUES, BUT IS A CALLABLE SO PASS EMPTY VALUES

                    $child->retrieve($instance, $child, []);

                } else if ($child->isEnforced()) {
                    throw new Exception($child->name() . " binding values are required.");
                }

            } else if($child instanceof Model) {
                if($dArr->exists($child->name())) {
                    $dKey = $dArr->find($child->name());
                    if(is_array($values[$dKey])) {
                        foreach($values[$dKey] as $value) {
                            $model = $child::create($value);
                            $child->insert($model);
                        }
                    } else {
                        $object->replace($i, $child::create($values[$dKey]));
                    }
                } else if($child->isEnforced()) {
                    throw new Exception($child->name() . " array values are required.");
                }
            } else {

                if($dArr->exists($child->name())) {
                    $dKey = $dArr->find($child->name());
                    if(is_array($values[$dKey])) {
                        foreach($values[$dKey] as $value) {
                            self::construct($instance, $child, $value, $child->path());
                        }
                    } else {
                        self::construct($instance, $child, $values[$dKey], $child->path());
                    }
                } else if($child->isEnforced()) {
                    throw new Exception($child->name() . " array values are required.");
                }
            }

            $i++;

        }

    }

    public function update(): void
    {

    }

}