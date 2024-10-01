<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{

    public function __construct(
        protected readonly UserRepository $userRepository,
    )
    {
    }

    public function createUser(string $name, string $email): array
    {
        $newUserId = $this->userRepository
            ->createUser($name, $email);

        $newUser = $this->userRepository
            ->getUserById($newUserId);

        return $newUser;
    }

    public function removeUser(int $userId): void
    {
        $this->userRepository
            ->removeUserById($userId);
    }
}