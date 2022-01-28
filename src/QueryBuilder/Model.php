<?php
declare(strict_types=1);

namespace Devcompru\DB\QueryBuilder;

use Devcompru\DB\DB\MariaDB;
use ReflectionClass;
use ReflectionProperty;

class Model
{
    const   INSERT = 'INSERT',
            SELECT = 'SELECT',
            UPDATE = 'UPDATE',
            DELETE = 'DELETE';


    private MariaDB $_sql;

    private $type = self::INSERT;
    protected QueryBuilder $query_builder;
    protected string $table = '';
    protected static Model $_instance;

    public function __construct($sql=false)
    {

        $this->_sql = $sql;
        $this->query_builder = new QueryBuilder();
        $this->table = mb_strtolower((new ReflectionClass(static::class))->getShortName());

        return $this;
    }

    public static function insert(array|object $values)
    {
        self::$_instance = new static;
        $query = self::$_instance->query_builder::insert($values)->table(self::$_instance->table)->create();
        $result = self::$_instance->_sql->query($query->sql, $query->params);
        return $result->rows();

    }

    public static function onDUplicateKey(array|object $values, string $action = 'UPDATE')
    {
        self::$_instance = new static;
        $query = self::$_instance->query_builder::insert($values)->table(self::$_instance->table)->onDuplicateKey()->create();
        $result = self::$_instance->_sql->query($query->sql, $query->params);

        return $result->rows();
    }

    public function create()
    {
        $data =  $this->fetchData();
        $query = $this->query_builder::insert($data)->table($this->table)->create();
        $sql = $this->_sql->query($query->sql, $query->params);
        if(property_exists($this, 'id'))
            $this->id = $sql->lastID();
        return $this;
    }


    public function delete()
    {


    }

    public function update()
    {



    }

    public function fetchData()
    {
        $data = [];
        $class = new ReflectionClass(static::class);

        $public_props = $class->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($public_props as $name=>$value)
        {
            $prop = $value->name;
            if(isset($this->$prop))
            {
                $data[$prop] = $this->$prop;
            }
        }

        return $data;

    }



}