<?php

namespace AppTests\Services;

use App\Repositories\UserRepository;
use App\Services\UserService;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{

    public function testCreateUser()
    {
        $userName = 'john doe';
        $userEmail = 'john@example.com';
        $userId = 5;

        // setup mock result data
        $mockedGetUserResult = [
            'id' => $userId,
            'is_active' => 1,
            'name' => $userName,
            'email' => $userEmail,
            'points_balance' => 0,
        ];

        // create mock of UserRepository
        $userRepositoryMock = $this->createPartialMock(UserRepository::class, ['createUser', 'getUserById']);

        // setup UserRepository mock to expect createUser called with $userName, $userEmail and to return $userId
        $userRepositoryMock->expects($this->once())
            ->method('createUser')
            ->with($userName, $userEmail)
            ->willReturn($userId);

        // setup UserRepository mock to expect getUserById called with $userId and to return mock result data
        $userRepositoryMock->expects($this->once())
            ->method('getUserById')
            ->with($userId)
            ->willReturn($mockedGetUserResult);

        // new instance of UserService using our mocks
        $userService = new UserService($userRepositoryMock);

        // call createUser to test our mocks are called as expected
        $userService->createUser($userName, $userEmail);
    }

    public function testRemoveUser()
    {

        $userIdToDelete = 12;

        // create mock of UserRepository
        $userRepositoryMock = $this->createPartialMock(UserRepository::class, ['removeUserById']);

        // setup UserRepository mock to expect removeUserById called with $userIdToDelete
        $userRepositoryMock->expects($this->once())
            ->method('removeUserById')
            ->with($userIdToDelete);

        // new instance of UserService using our mocks
        $userService = new UserService($userRepositoryMock);

        // call removeUser to test our mocks are called as expected
        $userService->removeUser(12);
    }
}
