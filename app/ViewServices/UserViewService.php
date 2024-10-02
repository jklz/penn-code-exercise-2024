<?php

namespace App\ViewServices;

use App\Exceptions\Http\FailedToCreateResourceHttpException;
use App\Exceptions\Http\ResourceNotFoundHttpException;
use App\Repositories\UserPointsActivityRepository;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\ViewModels\UserViewModel;


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
     * check that user exists
     * @throws ResourceNotFoundHttpException
     */
    public function verifyUserExists(int $userId): void
    {
        $doesUserExists = $this->userRepository
            ->doesUserExistById($userId);

        if (!$doesUserExists) {
            throw new ResourceNotFoundHttpException('User');
        }
    }

    /**
     * @throws ResourceNotFoundHttpException
     */
    public function getUser(int $userId): UserViewModel
    {
        $user = $this->userRepository
            ->getUserById($userId);

        if (!isset($user)) {
            throw new ResourceNotFoundHttpException('User');
        }

        return $this->userToViewModel($user);
    }

    /**
     * @throws FailedToCreateResourceHttpException
     */
    public function createUser(string $name, string $email): UserViewModel
    {
        $user = $this->userService
            ->createUser($name, $email);

        if (!isset($user)) {
            throw new FailedToCreateResourceHttpException('User');
        }

        return $this->userToViewModel($user);
    }

    /**
     * @throws ResourceNotFoundHttpException
     */
    public function removeUser(int $userId): void
    {
        $this->verifyUserExists($userId);

        $this->userService
            ->removeUser($userId);
    }

    /**
     * @throws ResourceNotFoundHttpException
     * @throws FailedToCreateResourceHttpException
     */
    public function createPointsEarnedForUser(int $userId, int $points, string $description): UserViewModel
    {
        // make sure user Exists
        $this->verifyUserExists($userId);

        try {
            $this->pointsActivityRepository
                ->createPointsEarnedActivity($userId, $points, $description);
        }catch (\Exception $exception) {
            throw new FailedToCreateResourceHttpException();
        }


            $user = $this->userRepository
            ->getUserById($userId);

        return $this->userToViewModel($user);
    }

    /**
     * @throws ResourceNotFoundHttpException
     * @throws FailedToCreateResourceHttpException
     */
    public function createPointsRedeemedForUser(int $userId, int $points, string $description): UserViewModel
    {
        // make sure user Exists
        $this->verifyUserExists($userId);

        try {
            $this->pointsActivityRepository
                ->createPointsRedeemedActivity($userId, $points, $description);
        } catch (\Exception $exception) {
            throw new FailedToCreateResourceHttpException();
        }

        $user = $this->userRepository
            ->getUserById($userId);

        return $this->userToViewModel($user);
    }
}