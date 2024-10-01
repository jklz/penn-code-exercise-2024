<?php

namespace App\Repositories;

use App\Models\User;
use App\Services\DatabaseService;
use PDO;

class UserRepository
{

    public function __construct(
        private DatabaseService $databaseService
    )
    {
    }

    public function getAll()
    {
        $sql = 'SELECT id, name, email, points_balance FROM users WHERE is_active = 1';

        return $this->databaseService
            ->fetchAll($sql);
    }

    public function getUserById(int $userId): ?array
    {

        $result = $this->databaseService
            ->fetchRow(
                'SELECT * FROM users WHERE id=? AND is_active = 1 LIMIT 1',
                [$userId]
            );

        if (!$result) {
            return null;
        }

        return $result;
    }

    public function doesUserExistById(int $userId): bool
    {
        $userCountForId = $this->databaseService
            ->fetchValue(
                'SELECT count(*) FROM users WHERE id= ? AND is_active = 1',
                [$userId]
            );
        return ($userCountForId === 1);
    }

    public function createUser(string $name, string $email): int
    {

        $newUserId = $this->databaseService
            ->insertRow(
                'INSERT INTO users (name, email) VALUES (?, ?)',
                [$name, $email]
            );

        return $newUserId;
    }

    public function removeUserById(int $userId)
    {
        $sql = 'UPDATE users SET is_active = 0 WHERE id=? AND is_active = 1';
        $this->databaseService
            ->execute($sql, [$userId]);
    }
}