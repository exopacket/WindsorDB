<?php

namespace Windsor\CLI;

use Windsor\DB\DBX;

class CreateSchema extends DBX
{

    public static function create()
    {
        self::connect();
        $con = self::$windsor;
        self::instances($con);
        self::withSearchable($con);
        self::withBindings($con);
        //self::withHooks($con);
    }

    private static function instances($con) {
        self::createTable($con, "instances", [
            'columns' => [
                'id' => [
                    'type' => 'varchar(40)'
                ],
                'model_key' => [
                    'type' => 'varchar(255)'
                ],
                'type' => [
                    'type' => 'varchar(255)'
                ],
                'path' => [
                    'type' => 'varchar(255)'
                ],
                'last_modified' => [
                    'type' => 'timestamp'
                ],
                'created_at' => [
                    'type' => 'timestamp'
                ]
            ],
            'primary' => 'id'
        ]);
    }

    private static function withSearchable($con) {
        self::createTable($con, "models_with_searchable", [
            'columns' => [
                'model_name' => [
                    'type' => 'varchar(255)'
                ],
                'field_name' => [
                    'type' => 'varchar(255)'
                ],
            ],
        ]);
    }

    private static function withBindings($con) {
        self::createTable($con, "models_with_bindings", [
            'columns' => [
                'model_name' => [
                    'type' => 'varchar(255)'
                ],
                'bind_name' => [
                    'type' => 'varchar(255)'
                ],
                'reference_name' => [
                    'type' => 'varchar(255)'
                ],
                'model_field_name' => [
                    'type' => 'varchar(255)'
                ],
                'type' => [
                    'type' => 'int'
                ]
            ],
        ]);
    }

}