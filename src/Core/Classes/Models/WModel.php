<?php

namespace Windsor\Core\Classes\Models;

use SimpleXMLElement;
use Windsor\Cloud\Cloud;
use Windsor\Core\Classes\Exception;
use Windsor\Core\Classes\Types\WOutput;
use Windsor\Core\Classes\WField;
use Windsor\Core\Classes\WObject;
use Windsor\Core\Classes\XQLBinding;
use Windsor\Core\Classes\XQLField;
use Windsor\Core\Classes\XQLModel;
use Windsor\Core\Classes\XQLObject;
use Windsor\Core\Supporting\BuildsModels;
use Windsor\Core\Supporting\BuildsQueries;
use Windsor\Core\Supporting\WritesFormattedFile;
use Windsor\Core\Utils\DynamicArr;

abstract class WModel extends WObject
{

    use BuildsQueries, BuildsModels, WritesFormattedFile;

    protected string $id;

    protected bool $static = false;
    protected bool $final = false;

    protected WPrimaryKey $primary;

    public function __construct(array $data = null) {
        $this->build();
        if(isset($data)) $this->populate($data);
        parent::__construct();
    }

    abstract protected function schema(XQLModel $model);

    protected function build()
    {
        $this->schema($this);
    }

    public function populate(array $data, WOutput $output) {
        if(array_key_exists("id", $data) && isset($data['id'])) $id = $data['id'];
        else $id = $this->id() ?? $this->generateId();
        $this->id = $id;
        $path = $this->modelKey(true) . "/" . $id . ".xml";
        $remote = simplexml_load_string(Cloud::get($path));
        $this->fill($remote);
    }

    public function fill($data)
    {
        $this->iterate($this, $data);
    }

    private function iterate(WObject $object, SimpleXMLElement $element)
    {
        $values = get_object_vars($element->children());
        $dArrSingle = new DynamicArr($values, "singular");
        $dArrMultiple = new DynamicArr($values, "plural");

        $i = 0;
        $children = $object->children();
        foreach($children as $child) {

            if($child instanceof WField) {
                if($child->isMultiple() && $dArrMultiple->exists($child->name())) {
                    $dKey = $dArrMultiple->find($child->name());
                    $multipleValues = $values[$dKey];
                    if($values[$dKey] instanceof SimpleXMLElement) $multipleValues = array_values(get_object_vars($values[$dKey]))[0];
                    if(is_array($multipleValues)) {
                        foreach ($multipleValues as $value) {
                            $child->appendMultiple($value);
                        }
                    }
                } else if($dArrSingle->exists($child->name())) {
                    $dKey = $dArrSingle->find($child->name());
                    $child->value($values[$dKey]);
                } else if($child->isEnforced()) {
                    throw new Exception($child->name() . " is required and were not found.");
                }
            } else if($child instanceof XQLBinding) {
                if (array_key_exists($child->fieldName(), $values)) {
                    $child->parse($values);
                }

            } else if($child instanceof XQLModel) {


                if($child->isMultiple() && $dArrMultiple->exists($child->name())) {

                    $dKey = $dArrMultiple->find($child->name());

                    $vals = get_object_vars((object) $values[$dKey]);

                    if(is_array(array_values($vals)[0])) {

                        $container = new XQLObject($child->groupName(), true);
                        foreach(array_values($vals)[0] as $value) {
                            $class = get_class($child);
                            $model = new $class();
                            $model->fill($value);
                            $container->appendChild($model);
                        }

                        $object->replace($i, $container);

                    } else {

                        $container = new XQLObject($child->groupName(), true);
                        $class = get_class($child);
                        $model = new $class();
                        $model->fill($values[$dKey]);
                        $container->appendChild($model);

                        $object->replace($i, $container);

                    }

                } else if($dArrSingle->exists($child->name())) {

                    $dKey = $dArrSingle->find($child->name());

                    $class = get_class($child);
                    //TODO get id attribute from xml for model instance
                    $model = new $class();
                    $model->fill($values[$dKey]);

                    $object->replace($i, $model);

                } else if($child->isEnforced()) {

                    throw new \Exception($child->name() . " is required and were not found.");

                }

            } else {
                if($dArrSingle->exists($child->name()) || $dArrMultiple->exists($child->name())) {
                    $dKey = $dArrSingle->find($child->name()) ? $dArrSingle->find($child->name()) : $dArrMultiple->find($child->name()) ;
                    $dataObject->{$child->name()} = (object)[];
                    $this->iterate($child, $values[$dKey], $dataObject->{$child->name()});
                }
            }

            $i++;

        }

    }

    protected function export(): string
    {
        $string = $this->xmlString(true);
        return $string;
    }

    public function id(): string
    {
        if(isset($this->primary)) {
            $object = $this->primary['object'];
            if($object instanceof XQLBinding) {

                $dbField = $this->primary['field'];
                $xpath = "0" . "." . $dbField;

                if($object->get($dbField) !== null) {
                    $val = $object->get($dbField);

                    if($val instanceof XQLField) {
                        $val = $val->value();
                    }

                    if (is_string($val) || is_numeric($val)) {
                        return $val;
                    }
                }

                if($object->get($xpath) !== null) {
                    $val = $object->get($xpath);

                    if($val instanceof XQLField) {
                        $val = $val->value();
                    }

                    if (is_string($val) || is_numeric($val)) {
                        return $val;
                    }
                }

            } else {
                return $object->id();
            }
        }
        if(!isset($this->id)) $this->generateId();
        return $this->id;
    }

    protected function generateId(): void
    {
        $data = get_called_class() . ":" . time() . ":" . microtime();
        $this->id = hash("sha1", $data);
    }

    public function modelKey(bool $plural = false, bool $camelCase = false)
    {
        $cases = $this->cases();
        $arr = ($plural) ? $cases['plural'] : $cases['singular'];
        return ($camelCase) ? $arr['camel'] : $arr['snake'];
    }

    public function isStatic()
    {
        return $this->static;
    }

    public function isFinal()
    {
        return $this->final;
    }

    public function toArray(): array
    {
        return json_decode(json_encode(simplexml_load_string($this->export())), true);
    }

}