<?php

namespace XQL\Core\Types;

enum Type: int
{
    case INTEGER = 1;
    case FLOAT = 2;
    case STRING = 3;
    case BOOLEAN = 4;
    case DATE = 5;
    case TIME = 6;
    case DATETIME = 7;
    case TIMESTAMP = 8;

    case XQL_OBJECT = 9;
    case XML = 10;
    case JSON = 11;
    case ARRAY = 12;
    case FILE = 13;

    case DYNAMIC = 14;
}

class TypeCollection {

    public static function fromStr(string $str): Type
    {
        return match (strtolower($str)) {
            "int", "integer" => Type::INTEGER,
            "float", "double", "decimal" => Type::FLOAT,
            "string", "str", "text" => Type::STRING,
            "bool", "boolean" => Type::BOOLEAN,
            "date" => Type::DATE,
            "time" => Type::TIME,
            "datetime" => Type::DATETIME,
            "timestamp" => Type::TIMESTAMP,
            "xql", "native" => Type::XQL_OBJECT,
            "xml", "simplexml" => Type::XML,
            "json" => Type::JSON,
            "array", "arr" => Type::ARRAY,
            "file" => Type::FILE,
            default => Type::DYNAMIC,
        };
    }

}