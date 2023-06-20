<?php

namespace Windsor\DB;

use Exception;
use PDO;

class DBX
{
    protected static PDO $data;
    protected static PDO $Windsor;

    protected static function createTable(PDO $con, string $tableName, array $config)
    {
        $query = "CREATE TABLE IF NOT EXISTS " . $tableName . "( ";
        $auto = !array_key_exists("primary", $config) && !array_key_exists("id", $config['columns']);
        if($auto) {
            $query .= "`id` int ";
        }
        $columns = array_keys($config['columns']);
        for($i=0; $i<count($columns); $i++) {
            if($i > 0 || $auto) $query .= ", ";
            $columnName = $columns[$i];
            $column = $config['columns'][$columnName];
            $type = $column['type'];
            $null = true;
            if($column['null'] === 'not' || $column['null'] === 'no') $null = false;
            $query .= "`" . $columnName . "` " . $type . (($null) ? '' : ' not') . ' null ';
        }
        if($auto) {
            $query .= ", primary key (`id`) ";
        } else {
            $query .= ", primary key (`" . $config['primary'] . "`)";
        }
        $query .= " )";
        $con->exec($query);

        if($auto) {
            $con->exec("alter table `" . $tableName . "` modify `id` int auto_increment");
        }
    }

    protected static function getSearchableFields(WindsorModel $model)
    {
        if(isset(self::$searchables['$model'])) return;
        self::connect();
        $con = self::$Windsor;
        $query = "SELECT id, field_name FROM models_with_searchable WHERE model_name=?";
        $stmt = $con->prepare($query);
        $stmt->bindValue(1, $model->modelKey());
        $stmt->execute();

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $arr = $stmt->fetchAll();
        if(count($arr) > 0) self::$searchables = array_merge(array($model->modelKey() => $arr), self::$searchables);
    }

    public static function updateSearchableFields(WindsorModel $instance, WindsorObject $field)
    {
        self::getSearchableFields($instance);
        if(isset(self::$searchables[$instance->modelKey()])) {
            $searchables = array_column(self::$searchables[$instance->modelKey()],  "field_name");
            if(in_array($field->fieldName(), $searchables)) return;
            self::connect();
            $con = self::$Windsor;
            $insertQuery = "INSERT INTO models_with_searchable(`model_name`, `field_name`) VALUES(?, ?)";
            $stmt = $con->prepare($insertQuery);
            $stmt->bindValue(1, $instance->modelKey());
            $stmt->bindValue(2, $field->fieldName());
            $stmt->execute();
            self::$searchables[$instance->modelKey()][] = $field->fieldName();
        } else {
            self::connect();
            $con = self::$Windsor;
            $insertQuery = "INSERT INTO models_with_searchable(`model_name`, `field_name`) VALUES(?, ?)";
            $stmt = $con->prepare($insertQuery);
            $stmt->bindValue(1, $instance->modelKey());
            $stmt->bindValue(2, $field->fieldName());
            $stmt->execute();
            $tableName = $instance->modelKey(true) . "_searchable";
            self::createTable($con, $tableName, [
                'columns' => [
                    'instance_id' => [
                        'type' => 'varchar(255)'
                    ],
                    'xpath' => [
                        'type' => 'varchar(255)'
                    ],
                    'field_name' => [
                        'type' => 'varchar(255)'
                    ],
                    'field_type' => [
                        'type' => 'varchar(255)'
                    ],
                    'string_value' => [
                        'type' => 'text'
                    ],
                    'integer_value' => [
                        'type' => 'int'
                    ],
                    'float_value' => [
                        'type' => 'float'
                    ],
                    'datetime_value' => [
                        'type' => 'timestamp'
                    ]
                ]
            ]);
        }
    }

    public static function insertSearchableValue(WindsorModel $instance, WindsorField $field, $value = null)
    {
        if(!isset($value)) $value = $field->value();
        $instance_id = $instance->id();
        $xpath = $field->xpath();
        $field_name = $field->fieldName();
        $field_type = $field->type();
        $dynamic_values = (new DynamicValue($value))->all();

        self::connect();
        $con = self::$Windsor;

        $tableName = $instance->modelKey(true) . "_searchable";

        $query = "INSERT INTO `" . $tableName .
            "` (`instance_id`, `xpath`, `field_name`, `field_type`, `string_value`, `integer_value`, `float_value`, `datetime_value`) VALUES(?,?,?,?,?,?,?,?)";

        $stmt = $con->prepare($query);
        $stmt->bindValue(1, $instance_id);
        $stmt->bindValue(2, $xpath);
        $stmt->bindValue(3, $field_name);
        $stmt->bindValue(4, $field_type->value);
        $stmt->bindValue(5, $dynamic_values['string']);
        $stmt->bindValue(6, $dynamic_values['integer']);
        $stmt->bindValue(7, $dynamic_values['float']);
        $stmt->bindValue(8, $dynamic_values['timestamp']);

        $stmt->execute();

    }

    public static function getBindedValues(string $table, array|string $columns, WindsorBindingClause $where, array $equals) {

        $conditions = $where->get();

        self::connect();
        $con = self::$data;
        $query = "SELECT";
        if(!is_array($columns)) {
            $query .= " " . $columns;
        } else {
            $first = true;
            foreach($columns as $column) {
                $query .= ((!$first) ? ", " : " ") . $column;
                $first = false;
            }
        }

        $query .= " FROM `" . $table . "` WHERE";

        $res = self::parseWhere($query, $conditions, $equals);
        $query = $res[0];
        $toBind = $res[1];

        $stmt = $con->prepare($query);
        $order = 1;
        for($i=0; $i<count($toBind); $i++) {
            $condition = $toBind[$i];
            if(is_array($condition['value'])) {
                foreach($condition['value'] as $value) {
                    $stmt->bindValue($order, $value);
                    $order++;
                }
            } else {
                $stmt->bindValue($order, $condition['value']);
                $order++;
            }
        }
        $stmt->execute();

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchAll();
    }

    protected static function parseWhere(string $query, array $conditions, array $equals, array $values = []): array
    {
        if(count($conditions) == 0) return [$query . " 1", $values];
        foreach($conditions as $condition) {
            if(array_key_exists("condition", $condition)) {
                $query .= " " . $condition['condition'];
            } else if(array_key_exists("column", $condition)) {
                if(!array_key_exists($condition['key'], $equals)) {
                    throw new Exception("Missing value for " . $condition['key'] . " (the column `" . $condition['column'] . "`).");
                }
                if(is_array($equals[$condition['key']])) {
                    $condition['value'] = $equals[$condition['key']];
                    $values[] = $condition;
                    $query .= " `" . $condition['column'] . "` IN (";
                    $first = true;
                    foreach($equals[$condition['key']] as $ignored) {
                        $query .= ($first) ? "?" : ", ?";
                        $first = false;
                    }
                    $query .= ")";
                } else {
                    $condition['value'] = $equals[$condition['key']];
                    $values[] = $condition;
                    $query .= " `" . $condition['column'] . "` = ?";
                }
            } else if(is_array($condition)) {
                $res = self::parseWhere($query, $condition, $equals, $values);
                $query = $res[0];
                $values = $res[1];
            }
        }
        return [$query, $values];
    }
    
    protected static function connect()
    {
        if(!isset(self::$data) || !isset(self::$Windsor)) {
            $WindsorDriver = Env::get("Windsor_DB_DRIVER");
            if($WindsorDriver == "mysql") {
                self::$Windsor = self::mysql(
                    Env::get("Windsor_DB_HOST"),
                    Env::get("Windsor_DB_PORT"),
                    Env::get("Windsor_DB_DATABASE"),
                    Env::get("Windsor_DB_USERNAME"),
                    Env::get("Windsor_DB_PASSWORD"));
            }

            $dataDriver = Env::get("Windsor_BINDED_DB_DRIVER");
            if($dataDriver == "mysql") {
                self::$data = self::mysql(
                    Env::get("Windsor_BINDED_DB_HOST"),
                    Env::get("Windsor_BINDED_DB_PORT"),
                    Env::get("Windsor_BINDED_DB_DATABASE"),
                    Env::get("Windsor_BINDED_DB_USERNAME"),
                    Env::get("Windsor_BINDED_DB_PASSWORD"));
            }
        }
    }

    protected static function mysql($host, $port, $database, $username, $password): PDO
    {
        $con = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $con;
    }
    
}