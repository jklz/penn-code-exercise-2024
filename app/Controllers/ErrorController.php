<?php

namespace App\Controllers;
use App\Controllers\Traits\JsonResponseTrait;
use Fig\Http\Message\StatusCodeInterface;
use Slim\Exception\HttpException;

class ErrorController
{
    use JsonResponseTrait;

    public function error(
        \Psr\Http\Message\ServerRequestInterface $request,
        \Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): \Slim\Psr7\Response|\Psr\Http\Message\ResponseInterface
    {
        // check if exception is should contain status code and message for response
        $isSlimHttpException = ($exception instanceof HttpException);

        if ($isSlimHttpException){
            // get error code and message from exception
            $code = $exception->getCode();
            $message = $exception->getMessage();
        } else {
            // output generic error code and message
            $code = 500;
            $message = "something went wrong. " . $exception->getMessage();
        }

        return $this->errorsJsonResponseWithCode($code, $message);
    }
}
