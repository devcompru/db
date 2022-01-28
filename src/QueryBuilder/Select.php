<?php
declare(strict_types=1);

namespace Devcompru\DB\QueryBuilder;



class Select extends AbstractQueryBuilder
{
    protected $join;



    public function getSQL()
    {
        $SQL = <<<SQL
SELECT {$this->columns}
FROM {$this->tablename}
{$this->join}
{$this->where}
SQL;
        return $SQL;
    }


    

}