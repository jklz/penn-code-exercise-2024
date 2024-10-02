<?php

namespace App\Controllers;

use App\Controllers\Traits\JsonResponseTrait;
use App\Controllers\Traits\RequestValidationTrait;
use App\Exceptions\Http\FailedToCreateResourceHttpException;
use App\Exceptions\Http\ValidationHttpException;
use App\Services\ValidationService;
use App\ViewServices\UserViewService;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController
{
    use JsonResponseTrait;
    use RequestValidationTrait;

    public function __construct(
        protected ValidationService $validationService,
        protected UserViewService $userViewService,
    )
    {
    }

    public function getList(Request $request, Response $response): \Slim\Psr7\Response|Response
    {
        $usersList = $this->userViewService
            ->listAllUsers();

        $jsonBody = [
            'data' => $usersList
        ];

        return $this->toJsonResponse($jsonBody, StatusCodeInterface::STATUS_OK, $response);

    }

    public function get(Request $request, Response $response): \Slim\Psr7\Response|Response
    {
        $userId = $request->getAttribute('userId');

        $user = $this->userViewService
                ->getUser($userId);

        return $this->toJsonDataResponse($user, StatusCodeInterface::STATUS_OK, $response);
    }

    /**
     * @throws FailedToCreateResourceHttpException
     * @throws ValidationHttpException
     */
    public function create(Request $request, Response $response): \Slim\Psr7\Response|Response
    {
        // configure field validation
        $this->validationService
            ->addFieldConfig('name', 'Name', 'string', true)
            ->addFieldConfig('email', 'email address', 'string', true);

        // check validation
        $this->validateJsonBodyValues($request);

        // get validated values
        $name = $this->validationService
            ->getSafeValue('name');
        $email = $this->validationService
            ->getSafeValue('email');

        $newUser = $this->userViewService
                ->createUser($name, $email);

        return $this->toJsonDataResponse($newUser, StatusCodeInterface::STATUS_CREATED, $response);
    }


    public function delete(Request $request, Response $response): \Slim\Psr7\Response|Response
    {
        $userId = $request->getAttribute('userId');

        $this->userViewService
            ->removeUser($userId);

        return $this->toJsonResponse(null, StatusCodeInterface::STATUS_NO_CONTENT, $response);
    }
}