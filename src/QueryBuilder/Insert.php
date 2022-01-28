<?php
declare(strict_types=1);

namespace Devcompru\DB\QueryBuilder;



use function is_array;
use function array_key_first;
use function array_values;
use function array_keys;
use function array_map;
use function implode;
use function str_replace;




class Insert extends AbstractQueryBuilder
{
    public string $table;
    public string $sql = 'INSERT INTO `{table_name}` ({columns})  VALUES {values} {on_duplicate}';

    private $replace =[
        '{table_name}'  => '',
        '{columns}'  => '',
        '{values}'  => '',
        '{on_duplicate}'  => '',
    ];

    public function __construct(array|object $data)
    {
        $this->table();
        $this->parseData($data);
        $this->columns();

        return $this;
    }


    private function build(): void
    {

        $this->setParams();
        $temp = [];

        foreach ($this->data as $key=>$value)
        {
            $temp[] = '('.implode(', ', array_values($value)).')';
        }
        $this->replace['{table_name}'] = $this->table;
        $this->replace['{columns}'] = $this->columns;
        $this->replace['{values}'] = implode(', ', $temp);
        $this->sql = str_replace(array_keys($this->replace), array_values($this->replace), $this->sql);
    }

    public function onDuplicateKey($action = 'UPDATE')
    {
        $temp = array_map(fn($el)=>"$el=VALUES($el)", $this->columns_array);
        $temp_2 = implode(', ', $temp);
        $this->replace['{on_duplicate}'] = "ON DUPLICATE KEY UPDATE ".$temp_2;
        return $this;
    }

    public function create(): QueryBuilderDTO
    {
        $this->build();
        $data = new QueryBuilderDTO();
        $data->sql = $this->sql;
        $data->params = $this->params;
        return $data;
    }


}