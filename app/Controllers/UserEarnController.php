<?php

namespace App\Controllers;

use App\Controllers\Traits\JsonResponseTrait;
use App\Controllers\Traits\RequestValidationTrait;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\ViewServices\UserViewService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserEarnController
{
    use JsonResponseTrait;
    use RequestValidationTrait;

    public function __construct(
        protected UserService $userService,
        protected UserViewService $userViewService,
        protected UserRepository $userRepository,
    )
    {
    }

    public function create(Request $request, Response $response)
    {
        $userId = $request->getAttribute('userId');

        //verify user exists
        $doesUserExist = $this->userRepository
            ->doesUserExistById($userId);
        if (!$doesUserExist) {
            return $this->notFoundErrorJsonResponse('User');
        }

        // configure field validation
        $this->addJsonFieldForValidation('points', 'Points earned', 'int', true);
        $this->addJsonFieldForValidation('description', 'Description of the transaction', 'string', true);

        // check validation
        if (!$this->isJsonBodyValidationSuccessful($request)) {
            return $this->errorJsonBodyValidationJsonResponse($response);
        }

        $validatedValues = $this->getValidatedJsonBodyValues();

        $user = $this->userViewService
            ->createPointsEarnedForUser($userId, $validatedValues['points'], $validatedValues['description']);

        $jsonBody = [
            'data' => $user
        ];
        return $this->toJsonResponse($jsonBody, 201, $response);
    }

}