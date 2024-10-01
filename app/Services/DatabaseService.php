<?php

namespace App\Services;

use PDO;

class DatabaseService
{

    private PDO $pdoConnection;

    private bool $isTransactionActive = false;


    public function getConnection(): PDO
    {
        if (isset($this->pdoConnection)) {
            return $this->pdoConnection;
        }

        $this->pdoConnection = new PDO('mysql:host=' . getenv('MYSQL_HOST') . ';dbname=' . getenv('MYSQL_DATABASE'), getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'));
        return $this->pdoConnection;
    }

    public function beginTransaction(): bool
    {
        if ($this->isTransactionActive) {
            throw new \Error('transaction already active.');
        }
        $this->isTransactionActive = true;
        return $this->getConnection()
            ->beginTransaction();
    }

    public function commitTransaction(): bool
    {
        if (!$this->isTransactionActive) {
            throw new \Error('no transaction active.');
        }
        $this->isTransactionActive = false;
        return $this->getConnection()
            ->commit();
    }

    public function rollBackTransaction(): bool
    {
        if (!$this->isTransactionActive) {
            throw new \Error('no transaction active.');
        }
        $this->isTransactionActive = false;
        return $this->getConnection()
            ->rollBack();
    }

    public function query(string $sql)
    {
        $queryStatement = $this->getConnection()
            ->query($sql);

        return $queryStatement->fetchAll(PDO::FETCH_ASSOC);
    }
    public function fetchAll(string $sql, array $params = null)
    {
        $queryStatement = $this->getConnection()
            ->prepare($sql);

        $queryStatement->execute($params);

        return $queryStatement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchRow(string $sql, array $params = null)
    {
        $queryStatement = $this->getConnection()
            ->prepare($sql);

        $queryStatement->execute($params);

        return $queryStatement->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchValue(string $sql, array $params = null)
    {
        $queryStatement = $this->getConnection()
            ->prepare($sql);

        $queryStatement->execute($params);

        return $queryStatement->fetchColumn();
    }

    public function execute(string $sql, array $params = null)
    {
        $queryStatement = $this->getConnection()
            ->prepare($sql);

        $queryStatement->execute($params);
    }

    public function insertRow(string $sql, array $params = null)
    {
        $this->execute($sql, $params);

        return $this->getConnection()
            ->lastInsertId();
    }

}