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

    /**
     * create user
     * @param string $name
     * @param string $email
     * @return array
     */
    public function createUser(string $name, string $email): array
    {
        $newUserId = $this->userRepository
            ->createUser($name, $email);

        $newUser = $this->userRepository
            ->getUserById($newUserId);

        return $newUser;
    }

    /**
     * remove user
     * @param int $userId
     * @return void
     */
    public function removeUser(int $userId): void
    {
        $this->userRepository
            ->removeUserById($userId);
    }
}