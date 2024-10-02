<?php

namespace App\Controllers\Traits;

use App\Exceptions\Http\HttpException;
use Fig\Http\Message\StatusCodeInterface;
use Slim\Exception\HttpException AS SlimHttpException;

trait JsonResponseTrait
{

    /**
     * return as error response
     * @param $body
     * @param int $statusCode
     * @param \Slim\Psr7\Response|null $response
     * @return \Slim\Psr7\Response|\Psr\Http\Message\ResponseInterface
     */
    private function jsonErrorResponse($body, int $statusCode, \Slim\Psr7\Response $response = null): \Slim\Psr7\Response|\Psr\Http\Message\ResponseInterface
    {

        // make sure we have a response
        $response ??= new \Slim\Psr7\Response();

        try {
            return $this->toJsonResponse($body, $statusCode, $response);
        } catch (\Exception $exception) {
            // status code didn't work, use internal error for status code
            return $this->toJsonResponse($body, StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, $response);
        }
    }

    /**
     * return simple error response with passed code and message
     * @param int $code
     * @param string $message
     * @param \Slim\Psr7\Response|null $response
     * @return \Slim\Psr7\Response|\Psr\Http\Message\ResponseInterface
     */
    private function errorsJsonResponseWithCode(int $code, string $message, \Slim\Psr7\Response $response = null): \Slim\Psr7\Response|\Psr\Http\Message\ResponseInterface
    {
        $responseBody = [
            'errors' => [],
        ];

        $responseBody['errors'][] = [
            'code' => $code,
            'message' => $message,

        ];

        return $this->jsonErrorResponse($responseBody, $code, $response);
    }

    /**
     * return error response from HttpException
     * @param HttpException $httpException
     * @return \Slim\Psr7\Response|\Psr\Http\Message\ResponseInterface
     */
    private function errorJsonResponseFromHttpException(HttpException $httpException): \Slim\Psr7\Response|\Psr\Http\Message\ResponseInterface
    {
        $statusCode = $httpException->getStatusCode();
        return $this->jsonErrorResponse($httpException, $statusCode);
    }

    /**
     * return error response from SlimHttpException
     * @param SlimHttpException $slimHttpException
     * @return \Slim\Psr7\Response|\Psr\Http\Message\ResponseInterface
     */
    private function errorJsonResponseFromSlimHttpException(SlimHttpException $slimHttpException): \Slim\Psr7\Response|\Psr\Http\Message\ResponseInterface
    {
        $statusCode = $slimHttpException->getCode();
        $message = $slimHttpException->getMessage();

        return $this->errorsJsonResponseWithCode($statusCode, $message);
    }

    /**
     * return json response with passed data under the property 'data'
     * @param $responseData
     * @param int $statusCode
     * @param \Slim\Psr7\Response|null $response
     * @return \Slim\Psr7\Response|\Psr\Http\Message\ResponseInterface
     *
     */
    private function toJsonDataResponse($responseData, int $statusCode = StatusCodeInterface::STATUS_OK, \Slim\Psr7\Response $response = null): \Slim\Psr7\Response|\Psr\Http\Message\ResponseInterface
    {
        return $this->toJsonResponse(['data' => $responseData], $statusCode, $response);
    }

    /**
     * return response as json with passed status code
     * @param $responseBody
     * @param int $statusCode
     * @param \Slim\Psr7\Response|null $response
     * @return \Slim\Psr7\Response|\Psr\Http\Message\ResponseInterface
     */
    private function toJsonResponse($responseBody, int $statusCode = StatusCodeInterface::STATUS_OK, \Slim\Psr7\Response $response = null): \Slim\Psr7\Response|\Psr\Http\Message\ResponseInterface
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