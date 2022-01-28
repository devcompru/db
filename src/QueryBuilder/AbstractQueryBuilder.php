<?php
declare(strict_types=1);

namespace Devcompru\DB\QueryBuilder;

use function is_array;
use function is_string;
use function count;
use function array_key_first;
use function array_keys;
use function array_values;
use function array_map;
use function implode;



use ReflectionClass;


class AbstractQueryBuilder
{

    protected string $where = '';
    protected array  $where_array = [];
    protected array  $where_params = [];

    protected string $columns = '*';
    protected array  $columns_array = [];
    protected array  $data = [];
    protected array  $values = [];

    protected array  $params = [];

    protected string $tablename = '';


    public function table(string $table = ''): static
    {
        $this->table = ($table)?$table:(new ReflectionClass(static::class))->getShortName();

        return $this;
    }


    /**
     * @param
     * @param string column
     * @param string operand
     * @param string value
     */
    public function where(array $where): static
    {
        if(is_array($where))
        {
            if (count($where) == 2)
            {
                $this->where_array[] = [$where[0], '=', $where[1]];
            } else
            {
                $this->where_array[] = [$where[0], $where[1], $where[2]];
            }
        }
        elseif (is_string($where))
        {
            $this->where = $where;
        }
        else
        {
            // TODO: Сделать исключение
        }
        return $this;
    }


    protected function parseData(array|object $data): static
    {
        $data = (array)$data;
        if(!is_array($data[array_key_first($data)]))
        {
            $this->data = [$data];
        }
        else
        {
            $this->data = $data;
        }


        return $this;
    }

    public function setParams(): static
    {
        $count = 1;
        foreach ($this->data as $key=>$data)
        {
            foreach ($data as $name=>$value)
            {
                $this->params[$name.'_'.$count] = "$value";
                $this->data[$key][$name] = ':'.$name.'_'.$count;
            }
            $count++;
        }

        return $this;
    }

    public function columns(string|array $columns = ''): static
    {
        if($columns=='')
        {
            $temp_array = array_keys($this->data[0]);
            $this->columns_array = $temp_array;
            $temp_array = array_map(fn($el)=>"`$el`", $temp_array);
            $this->columns = implode(',',$temp_array);
        }
        elseif (is_string($columns))
        {
            $this->columns = $columns;
        }
        elseif (is_array($columns))
        {
            $this->columns = implode(',',$columns);
        }


        return $this;
    }









}