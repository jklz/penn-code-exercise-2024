<?php

namespace App\Repositories;

use App\Services\DatabaseService;

class UserPointsActivityRepository
{

    public function __construct(
        private DatabaseService $databaseService
    )
    {
    }

    private function updateUserPointsFromActivity(int $userId): void
    {
        $sql = 'UPDATE users SET points_balance = (SELECT SUM(points) FROM user_points_activity WHERE user_id = ?) WHERE id=?';
        $this->databaseService
            ->execute($sql, [$userId, $userId]);
    }

    private function createPointsActivity(int $userId, int $points, string $description): void
    {
        $sql = 'INSERT INTO user_points_activity (user_id, points, description) VALUES (?, ?, ?)';

        $this->databaseService
            ->beginTransaction();

        try {
            $this->databaseService
                ->execute($sql, [$userId, $points, $description]);

            $this->updateUserPointsFromActivity($userId);

            $this->databaseService
                ->commitTransaction();
        } catch (\Exception $exception) {
            // had error, roll back transaction
            $this->databaseService
                ->rollBackTransaction();
            // rethrow error
            throw $exception;
        }
    }

    public function createPointsEarnedActivity(int $userId, int $pointsEarned, string $description): void
    {
        $this->createPointsActivity($userId, $pointsEarned, $description);
    }

    public function createPointsRedeemedActivity(int $userId, int $pointsRedeemed, string $description): void
    {
        $this->createPointsActivity($userId, $pointsRedeemed * -1, $description);
    }
}