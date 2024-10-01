<?php

namespace App\Clients\Database;

use PDO;

class DatabaseClient implements DatabaseClientInterface
{

    private PDO $pdo;

    public function getConnection(): PDO
    {

        if (isset($this->pdo)) {
            return $this->pdo;
        }

        return new PDO('mysql:host=' . getenv('MYSQL_HOST') . ';dbname=' . getenv('MYSQL_DATABASE'));
    }

}