<?php

namespace src\DB;

use PDO;
use PDOStatement;

/**
 * Class DBAccess
 * @package DB
 */
class DBAccess
{
    private $dbh;

    public function __construct()
    {
        $dsn = 'mysql:dbname=chat;host=127.0.0.1';
        $user = 'user';
        $password = 'user';

        $this->dbh = new PDO($dsn, $user, $password);
    }

    /**
     * @param string $sql
     * @param array $arrayDate
     * @return bool|PDOStatement
     */
    public function getSQLExecution(string $sql, array $arrayData)
    {
        $sth = $this->dbh->prepare($sql);
        $sth->execute($arrayData);

        return $sth;
    }
}