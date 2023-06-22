<?php

namespace XQL\Core\Types;

enum WType: int
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

class WTypeCollection {

    public static function fromStr(string $str): WType
    {
        return match (strtolower($str)) {
            "int", "integer" => WType::INTEGER,
            "float", "double", "decimal" => WType::FLOAT,
            "string", "str", "text" => WType::STRING,
            "bool", "boolean" => WType::BOOLEAN,
            "date" => WType::DATE,
            "time" => WType::TIME,
            "datetime" => WType::DATETIME,
            "timestamp" => WType::TIMESTAMP,
            "xql", "native" => WType::XQL_OBJECT,
            "xml", "simplexml" => WType::XML,
            "json" => WType::JSON,
            "array", "arr" => WType::ARRAY,
            "file" => WType::FILE,
            default => WType::DYNAMIC,
        };
    }

}