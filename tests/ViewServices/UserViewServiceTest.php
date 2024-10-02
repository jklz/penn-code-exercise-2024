<?php

namespace AppTests\ViewServices;

use App\Exceptions\Http\ResourceNotFoundHttpException;
use App\Repositories\UserPointsActivityRepository;
use App\Repositories\UserRepository;
use App\Services\DatabaseService;
use App\Services\UserService;
use App\ViewModels\UserViewModel;
use App\ViewServices\UserViewService;
use PHPUnit\Framework\TestCase;

class UserViewServiceTest extends TestCase
{

    public function testVerifyUserExistsHitsRepository()
    {

        $userId = 14;

        // create mock of UserRepository
        $userRepositoryMock = $this->createPartialMock(UserRepository::class, ['doesUserExistById']);

        // setup UserRepository mock to expect doesUserExistById called with $userId and to return true
        $userRepositoryMock->expects($this->once())
            ->method('doesUserExistById')
            ->with($userId)
            ->willReturn(true);

        // new instance of UserViewService using our mock
        $userViewService = new UserViewService(
            new UserService($userRepositoryMock),
            $userRepositoryMock,
            new UserPointsActivityRepository( new DatabaseService())
        );

        // call verifyUserExists with $userId to test configured mock calls are called
        $userViewService->verifyUserExists($userId);

        // ensure test reaches here without error thrown
        $this->assertTrue(true);

    }
    public function testVerifyUserExistsThrowsExceptionWhenMissing()
    {
        $userId = 14;

        // create mock of UserRepository
        $userRepositoryMock = $this->createPartialMock(UserRepository::class, ['doesUserExistById']);

        // setup UserRepository mock to expect doesUserExistById called with $userId and to return false
        $userRepositoryMock->expects($this->once())
            ->method('doesUserExistById')
            ->with($userId)
            ->willReturn(false);

        // new instance of UserViewService using our mock
        $userViewService = new UserViewService(
            new UserService($userRepositoryMock),
            $userRepositoryMock,
            new UserPointsActivityRepository( new DatabaseService())
        );

        // setup test to expect exception to be thrown
        $this->expectException(ResourceNotFoundHttpException::class);

        // call verifyUserExists with $userId to test configured mock calls are called and exception is thrown
        $userViewService->verifyUserExists($userId);
    }

    public function testCreatePointsEarnedForUser()
    {
        $userId = 14;
        $pointsEarned = 500;
        $description = "test-description";

        // setup mock result data
        $mockedGetUserResult = [
            'id' => $userId,
            'is_active' => 1,
            'name' => 'name',
            'email' => 'email',
            'points_balance' => 0,
        ];

        // create mock of UserRepository
        $userRepositoryMock = $this->createPartialMock(UserRepository::class, ['doesUserExistById', 'getUserById']);

        // setup UserRepository mock to expect doesUserExistById called with $userId and to return true
        $userRepositoryMock->expects($this->once())
            ->method('doesUserExistById')
            ->with($userId)
            ->willReturn(true);

        // setup UserRepository mock to expect getUserById called with $userId and to return true
        $userRepositoryMock->expects($this->once())
            ->method('getUserById')
            ->with($userId)
            ->willReturn($mockedGetUserResult);

        // create mock of UserPointsActivityRepository
        $userPointsActivityRepositoryMock = $this->createPartialMock(UserPointsActivityRepository::class, ['createPointsEarnedActivity']);

        // setup UserRepository mock to expect getUserById called with $userId, $pointsEarned, $description with no return
        $userPointsActivityRepositoryMock->expects($this->once())
            ->method('createPointsEarnedActivity')
            ->with($userId, $pointsEarned, $description);

        // new instance of UserViewService using our mocks
        $userViewService = new UserViewService(
            new UserService($userRepositoryMock),
            $userRepositoryMock,
            $userPointsActivityRepositoryMock,
        );

        // call createPointsEarnedForUser with $userId, $pointsEarned, $description and test our mocks are called as expected
        $userViewModel = $userViewService->createPointsEarnedForUser($userId, $pointsEarned, $description);

        // verify returned value instance of UserViewModel
        $this->assertInstanceOf(UserViewModel::class, $userViewModel);

    }

    public function testListAllUsers()
    {
        // setup mock result data
        $mockedGetAllUserResult = [
            [
                'id' => 1,
                'is_active' => 1,
                'name' => 'name a',
                'email' => 'email a',
                'points_balance' => 0,
            ],
            [
                'id' => 2,
                'is_active' => 1,
                'name' => 'name b',
                'email' => 'email b',
                'points_balance' => 0,
            ],
        ];

        // create mock of UserRepository
        $userRepositoryMock = $this->createPartialMock(UserRepository::class, ['getAll']);

        // setup UserRepository mock to expect getAll called once and to return mock result data
        $userRepositoryMock->expects($this->once())
            ->method('getAll')
            ->with()
            ->willReturn($mockedGetAllUserResult);

        // new instance of UserViewService using our mocks
        $userViewService = new UserViewService(
            new UserService($userRepositoryMock),
            $userRepositoryMock,
            new UserPointsActivityRepository( new DatabaseService())
        );

        // call listAllUsers and test our mocks are called as expected
        $userListResult = $userViewService->listAllUsers();

        // verify return count is the same as the configured mock result data
        $this->assertCount(count($mockedGetAllUserResult), $userListResult);

        foreach ($userListResult AS $user) {
            // verify each item in result is instance of UserViewModel
            $this->assertInstanceOf(UserViewModel::class, $user);
        }

    }

    public function testCreatePointsRedeemedForUser()
    {
        $userId = 14;
        $pointsEarned = 500;
        $description = "test-description";

        // setup mock result data
        $mockedGetUserResult = [
            'id' => $userId,
            'is_active' => 1,
            'name' => 'name',
            'email' => 'email',
            'points_balance' => 0,
        ];

        // create mock of UserRepository
        $userRepositoryMock = $this->createPartialMock(UserRepository::class, ['doesUserExistById', 'getUserById']);

        // setup UserRepository mock to expect doesUserExistById called once with $userId and to return true
        $userRepositoryMock->expects($this->once())
            ->method('doesUserExistById')
            ->with($userId)
            ->willReturn(true);

        // setup UserRepository mock to expect getUserById called once with $userId and to return mock result data
        $userRepositoryMock->expects($this->once())
            ->method('getUserById')
            ->with($userId)
            ->willReturn($mockedGetUserResult);

        // create mock of UserPointsActivityRepository
        $userPointsActivityRepositoryMock = $this->createPartialMock(UserPointsActivityRepository::class, ['createPointsRedeemedActivity']);

        // setup UserPointsActivityRepository mock to expect createPointsRedeemedActivity called once with $userId, $pointsEarned, $description
        $userPointsActivityRepositoryMock->expects($this->once())
            ->method('createPointsRedeemedActivity')
            ->with($userId, $pointsEarned, $description);

        // new instance of UserViewService using our mocks
        $userViewService = new UserViewService(
            new UserService($userRepositoryMock),
            $userRepositoryMock,
            $userPointsActivityRepositoryMock,
        );

        // call createPointsRedeemedForUser and test our mocks are called as expected
        $userViewModel = $userViewService->createPointsRedeemedForUser($userId, $pointsEarned, $description);

        // verify returned value instance of UserViewModel
        $this->assertInstanceOf(UserViewModel::class, $userViewModel);
    }

    public function testRemoveUser()
    {
        $userId = 14;

        // create mock of UserRepository
        $userRepositoryMock = $this->createPartialMock(UserRepository::class, ['doesUserExistById', 'removeUserById']);

        // setup UserRepository mock to expect doesUserExistById called once with $userId and to return true
        $userRepositoryMock->expects($this->once())
            ->method('doesUserExistById')
            ->with($userId)
            ->willReturn(true);

        // create mock of UserService
        $userServiceMock = $this->createPartialMock(UserService::class, ['removeUser']);

        // setup UserService mock to expect removeUser called once with $userId
        $userServiceMock->expects($this->once())
            ->method('removeUser')
            ->with($userId);

        // new instance of UserViewService using our mocks
        $userViewService = new UserViewService(
            $userServiceMock,
            $userRepositoryMock,
            new UserPointsActivityRepository( new DatabaseService())
        );

        // call removeUser and test our mocks are called as expected
        $userViewService->removeUser($userId);

    }

    public function testGetUser()
    {
        $userId = 14;

        // setup mock result data
        $mockedGetUserResult = [
            'id' => $userId,
            'is_active' => 1,
            'name' => 'name',
            'email' => 'email',
            'points_balance' => 0,
        ];

        // create mock of UserRepository
        $userRepositoryMock = $this->createPartialMock(UserRepository::class, ['getUserById']);

        // setup UserRepository mock to expect getUserById called once with $userId and to return mock result data
        $userRepositoryMock->expects($this->once())
            ->method('getUserById')
            ->with($userId)
            ->willReturn($mockedGetUserResult);

        // new instance of UserViewService using our mocks
        $userViewService = new UserViewService(
            new UserService($userRepositoryMock),
            $userRepositoryMock,
            new UserPointsActivityRepository( new DatabaseService())
        );
        // call getUser and test our mocks are called as expected
        $userViewModel = $userViewService->getUser($userId);

        // verify returned value instance of UserViewModel
        $this->assertInstanceOf(UserViewModel::class, $userViewModel);
    }

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


        // create mock of UserService
        $userServiceMock = $this->createPartialMock(UserService::class, ['createUser']);

        // setup UserService mock to expect createUser called once with $userName, $userEmail and to return mock result data
        $userServiceMock->expects($this->once())
            ->method('createUser')
            ->with($userName, $userEmail)
            ->willReturn($mockedGetUserResult);

        // new instance of UserViewService using our mocks
        $userViewService = new UserViewService(
            $userServiceMock,
            new UserRepository(new DatabaseService()),
            new UserPointsActivityRepository( new DatabaseService())
        );

        // call createUser and test our mocks are called as expected
        $userViewModel = $userViewService->createUser($userName, $userEmail);

        // verify returned value instance of UserViewModel
        $this->assertInstanceOf(UserViewModel::class, $userViewModel);
    }
}
