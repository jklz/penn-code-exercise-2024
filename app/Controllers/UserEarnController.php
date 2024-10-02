<?php

namespace App\Controllers;

use App\Controllers\Traits\JsonResponseTrait;
use App\Controllers\Traits\RequestValidationTrait;
use App\Exceptions\Http\FailedToCreateResourceHttpException;
use App\Exceptions\Http\ResourceNotFoundHttpException;
use App\Exceptions\Http\ValidationHttpException;
use App\Services\ValidationService;
use App\ViewServices\UserViewService;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserEarnController
{
    use JsonResponseTrait;
    use RequestValidationTrait;

    public function __construct(
        protected ValidationService $validationService,
        protected UserViewService $userViewService,
    )
    {
    }

    /**
     * @throws FailedToCreateResourceHttpException
     * @throws ResourceNotFoundHttpException
     * @throws ValidationHttpException
     */
    public function create(Request $request, Response $response): \Slim\Psr7\Response|Response
    {
        $userId = $request->getAttribute('userId');

        // configure field validation
        $this->validationService
            ->addFieldConfig('points', 'Points earned', 'int', true)
            ->addFieldConfig('description', 'Description of the transaction', 'string', true);

        // check validation
        $this->validateJsonBodyValues($request);

        // get validated values
        $points = $this->validationService
            ->getSafeValue('points');
        $description = $this->validationService
            ->getSafeValue('description');

        $user = $this->userViewService
            ->createPointsEarnedForUser($userId, $points, $description);

        return $this->toJsonDataResponse($user, StatusCodeInterface::STATUS_CREATED, $response);
    }

}