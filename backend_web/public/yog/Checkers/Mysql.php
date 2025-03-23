<?php

namespace Yog\Checkers;

final readonly class Mysql
{
    public function __construct()
    {
        if (!defined("MYSQLI_TYPE_BIT"))
            define("MYSQLI_TYPE_BIT", 16);
    }

    public static function getInstance(): self
    {
        return new self();
    }

    public function getLiteralMysqlTypeByMysqlTypeId(int $typeId): string
    {
        return match ($typeId) {
            MYSQLI_TYPE_TINY => "tinyint",
            MYSQLI_TYPE_SHORT => "shortint",
            MYSQLI_TYPE_LONG => "int",
            MYSQLI_TYPE_FLOAT => "float",
            MYSQLI_TYPE_DOUBLE => "double",
            MYSQLI_TYPE_NULL => "default null",
            MYSQLI_TYPE_TIMESTAMP => "timestamp",
            MYSQLI_TYPE_BIT => "bit",
            MYSQLI_TYPE_LONGLONG => "bigint",
            MYSQLI_TYPE_INT24 => "mediumint",
            MYSQLI_TYPE_DATE => "date",
            MYSQLI_TYPE_TIME => "time",
            MYSQLI_TYPE_DATETIME => "datetime",
            MYSQLI_TYPE_YEAR => "year",
            MYSQLI_TYPE_NEWDATE => "date",
            MYSQLI_TYPE_ENUM => "enum",
            MYSQLI_TYPE_SET => "set",
            MYSQLI_TYPE_TINY_BLOB => "tinyblob",
            MYSQLI_TYPE_MEDIUM_BLOB => "mediumblob",
            MYSQLI_TYPE_LONG_BLOB => "longblob",
            MYSQLI_TYPE_BLOB => "blob",
            MYSQLI_TYPE_VAR_STRING => "varchar",
            MYSQLI_TYPE_STRING => "char",
            MYSQLI_TYPE_GEOMETRY => "geometry",
            MYSQLI_TYPE_NEWDECIMAL => "newdecimal",
            MYSQLI_TYPE_JSON => "json",
            default => "unknown",
        };
    }
}