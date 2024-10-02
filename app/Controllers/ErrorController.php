<?php

namespace App\Controllers;
use App\Controllers\Traits\JsonResponseTrait;
use App\Exceptions\Http\HttpException;
use Fig\Http\Message\StatusCodeInterface;
use Slim\Exception\HttpException AS SlimHttpException;

class ErrorController
{
    use JsonResponseTrait;



    public function handleException(
        \Psr\Http\Message\ServerRequestInterface $request,
        \Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): \Slim\Psr7\Response|\Psr\Http\Message\ResponseInterface
    {

        // check if exception extends HttpException
        if ($exception instanceof HttpException) {
            return $this->errorJsonResponseFromHttpException($exception);
        }

        // check if exception extends SlimHttpException
        if ($exception instanceof SlimHttpException) {
            return $this->errorJsonResponseFromSlimHttpException($exception);
        }

        $message = "something went wrong. " . $exception->getMessage();
        return $this->errorsJsonResponseWithCode(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, $message);
    }
}
