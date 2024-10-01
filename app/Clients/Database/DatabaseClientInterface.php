<?php

namespace App\Clients\Database;

interface DatabaseClientInterface
{
    /**
     * @param string $sql
     * @return void
     */
    public function exec(string $sql): void;

    /**
     * @param string $sql
     * @param array $params
     * @return void
     */
    public function fetch(string $sql, array $params = []): array;
}