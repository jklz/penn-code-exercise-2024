<?php

namespace App\ViewServices;

use App\Models\User;
use App\Repositories\UserPointsActivityRepository;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\ViewModels\UserViewModel;
use App\ViewServices\Exception\ResourceNotFoundException;

class UserViewService
{
    public function __construct(
        protected readonly UserService $userService,
        protected readonly UserRepository $userRepository,
        protected readonly UserPointsActivityRepository $pointsActivityRepository,
    )
    {
    }

    protected function userToViewModel($user): UserViewModel
    {
        return new UserViewModel(
            $user['id'],
            $user['name'],
            $user['email'],
            $user['points_balance'],
        );
    }

    public function listAllUsers()
    {
        $allUsers = $this->userRepository
            ->getAll();

        $allUsersResponse = array_map(
            fn($user) => $this->userToViewModel($user),
            $allUsers,
        );

        return $allUsersResponse;
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function getUser(int $userId): UserViewModel
    {
        $user = $this->userRepository
            ->getUserById($userId);

        if (!isset($user)) {
            throw new ResourceNotFoundException();
        }

        return $this->userToViewModel($user);
    }

    public function createUser(string $name, string $email): UserViewModel
    {
        $user = $this->userService
            ->createUser($name, $email);

        if (!isset($user)) {
            throw new ResourceNotFoundException();
        }

        return $this->userToViewModel($user);
    }

    public function createPointsEarnedForUser(int $userId, int $points, string $description): UserViewModel
    {
        $this->pointsActivityRepository
            ->createPointsEarnedActivity($userId, $points, $description);

        $user = $this->userRepository
            ->getUserById($userId);

        return $this->userToViewModel($user);
    }

    public function createPointsRedeemedForUser(int $userId, int $points, string $description): UserViewModel
    {
        $this->pointsActivityRepository
            ->createPointsRedeemedActivity($userId, $points, $description);

        $user = $this->userRepository
            ->getUserById($userId);

        return $this->userToViewModel($user);
    }
}