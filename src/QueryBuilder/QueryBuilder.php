<?php
declare(strict_types=1);

namespace Devcompru\DB\QueryBuilder;

class QueryBuilder
{

    private static Insert $_insert;


    public static function insert(array|object $data):Insert
    {
        self::$_insert = new Insert(($data));
        return self::$_insert;
    }





}