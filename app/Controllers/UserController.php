<?php

namespace App\Controllers;

use App\Controllers\Traits\JsonResponseTrait;
use App\Controllers\Traits\RequestValidationTrait;
use App\Services\UserService;
use App\ViewServices\Exception\ResourceNotFoundException;
use App\ViewServices\UserViewService;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function DI\string;

class UserController
{
    use JsonResponseTrait;
    use RequestValidationTrait;

    public function __construct(
        protected UserService $userService,
        protected UserViewService $userViewService,
    )
    {
    }

    public function getList(Request $request, Response $response)
    {
        $usersList = $this->userViewService
            ->listAllUsers();

        $jsonBody = [
            'data' => $usersList
        ];

        return $this->toJsonResponse($jsonBody, 200, $response);

    }

    public function get(Request $request, Response $response)
    {

        $userId = $request->getAttribute('userId');

        try {

            $user = $this->userViewService
                ->getUser($userId);

            $jsonBody = [
                'data' => $user
            ];
        } catch (ResourceNotFoundException $exception) {
            return $this->notFoundErrorJsonResponse('User', $response);
        } catch (\Exception $exception) {
            return $this->internalErrorJsonResponse($response);
        }

        return $this->toJsonResponse($jsonBody, 200, $response);
    }

    public function create(Request $request, Response $response)
    {
        // configure field validation
        $this->addJsonFieldForValidation('name', 'Name', 'string', true);
        $this->addJsonFieldForValidation('email', 'email address', 'string', true);

        // check validation
        if (!$this->isJsonBodyValidationSuccessful($request)) {
            return $this->errorJsonBodyValidationJsonResponse($response);
        }

        $validatedValues = $this->getValidatedJsonBodyValues();

        try {
            $newUser = $this->userViewService
                ->createUser($validatedValues['name'], $validatedValues['email']);
            $jsonBody = [
                'data' => $newUser
            ];
        } catch (\Exception $exception) {
            return $this->internalErrorJsonResponse($response);
        }

        return $this->toJsonResponse($jsonBody, 201, $response);
    }


    public function delete(Request $request, Response $response)
    {

//        $response->withStatus(200)
//            ->getBody()
//            ->write(json_encode(['data' => ['user1', 'user2']]));

//        return $response;
    }
}