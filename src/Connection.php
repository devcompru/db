<?php
declare(strict_types=1);

namespace Devcompru\DB;

use PDO;
use PDOException;
use PDOStatement;
use Exception;



class Connection
{
    private PDO $_pdo;
    private PDOStatement $_statement;
    private array $_config;

    /**
     * @param array $config
     * @param $hostname
     * @param $database
     * @param $charset
     * @param $username
     * @param $password
     */

    function __construct(array $config)
    {
        $this->_config = $config;
        $this->connect();

    }

    public function connect(): Connection
    {
        $dsn = "mysql:host={$this->_config['hostname']};dbname={$this->_config['database']};charset={$this->_config['charset']}";
        //MOX($dsn);
        $opt = [
            PDO::ATTR_PERSISTENT         => true,
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->_pdo = new PDO($dsn, $this->_config['username'], $this->_config['password'], $opt);
        }
        catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
        unset($this->_config);
        return $this;

    }
    public function query($sql, $params = []):static
    {


        $this->_statement = $this->_pdo->prepare($sql);

        $this->_statement->execute($params);
        return $this;
    }
    public function fetch($object = null, $fetch_mode = false)
    {
        if(!is_null($object))
            $this->_statement->setFetchMode(PDO::FETCH_CLASS, $object);

        if($fetch_mode)
            return $fetch = $this->_statement->fetch($fetch_mode);

        $fetch = $this->_statement->fetch();

        return $fetch;
    }
    public function fetchAll($object = null, $fetch_mode = false)
    {
        if(!is_null($object))
            $this->_statement->setFetchMode(PDO::FETCH_CLASS, $object);

        if($fetch_mode)
            return $fetch = $this->_statement->fetchAll($fetch_mode);

        return $fetch = $this->_statement->fetchAll();

    }
    public function statement():PDOStatement
    {
        return $this->_statement;

    }
    public function pdo():PDO
    {
        return $this->_pdo;

    }
    public function rows()
    {
        return $this->_statement->rowCount();
    }
    public function lastID()
    {
        return $this->_pdo->lastInsertId();
    }


}