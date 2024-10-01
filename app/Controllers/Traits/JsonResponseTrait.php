<?php

namespace App\Controllers\Traits;

use Fig\Http\Message\StatusCodeInterface;

trait JsonResponseTrait
{

    private function errorJsonResponse(array $errors, int $statusCode, \Slim\Psr7\Response $response = null): \Slim\Psr7\Response|\Psr\Http\Message\ResponseInterface
    {
        $errorBody =  [
            'errors' => $errors,
        ];

        // make sure we have a response
        $response ??= new \Slim\Psr7\Response();


        try {
            return $this->toJsonResponse($errorBody, $statusCode, $response);
        } catch (\Exception $exception) {
            // status code didn't work, use internal error for status code
            return $this->toJsonResponse($errorBody, StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, $response);
        }
    }

    private function errorsJsonResponseWithCode(int $code, string $message, \Slim\Psr7\Response $response = null): \Slim\Psr7\Response|\Psr\Http\Message\ResponseInterface
    {
        $errors = [];
        $errors[] = [
            'code' => $code,
            'message' => $message,
        ];

        return $this->errorJsonResponse($errors, $code, $response);
    }

    private function notFoundErrorJsonResponse(string $resource = 'resource', \Slim\Psr7\Response $response = null): \Slim\Psr7\Response|\Psr\Http\Message\ResponseInterface
    {
        $message = $resource . ' not found.';
        return $this->errorsJsonResponseWithCode(404, $message, $response);
    }

    private function internalErrorJsonResponse(\Slim\Psr7\Response $response = null): \Slim\Psr7\Response|\Psr\Http\Message\ResponseInterface
    {
        return $this->errorsJsonResponseWithCode(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, 'Internal Server Error', $response);
    }

    private function toJsonResponse($responseBody, int $statusCode = 200, \Slim\Psr7\Response $response = null): \Slim\Psr7\Response|\Psr\Http\Message\ResponseInterface
    {
        // make sure we have a response
        $response ??= new \Slim\Psr7\Response();

        // ensure we have response body as a string;
        if (!is_string($responseBody)) {
            // was not already a string, so json encode
            $responseBody = json_encode($responseBody);
        }

        // set response body to be our string
        $response->getBody()
            ->write($responseBody);

        return $response->withStatus($statusCode)
            ->withAddedHeader('Content-Type', 'application/json');
    }

}