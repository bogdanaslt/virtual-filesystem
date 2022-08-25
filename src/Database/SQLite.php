<?php

namespace App\Database;

use PDO;

class SQLite
{
    private ?PDO $connection = null;

    public function __construct()
    {
        $this->connection = new PDO('sqlite:'.__DIR__.'/../../storage/db.sq3');
        $this->connection->exec('PRAGMA foreign_keys = ON');
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}