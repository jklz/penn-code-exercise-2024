<?php

namespace App\Exceptions\Http;

use Fig\Http\Message\StatusCodeInterface;

abstract class HttpException extends \Exception implements \JsonSerializable, StatusCodeInterface
{
    public function __construct(
        protected array $responseErrors = [],
        ?\Throwable $previous = null,
    )
    {
        parent::__construct('', $this->getStatusCode(), $previous);
    }

    /**
     * Get Http response status code
     * @return int
     */
    abstract public function getStatusCode(): int;

    /**
     * Add error message to be included in body
     * @param array $error
     * @return $this
     */
    public function addError(array $error): self
    {
        $this->responseErrors[] = $error;
        return $this;
    }

    /**
     * get all errors that have been included in body
     * @return array|array[]
     */
    public function getErrors(): array
    {
        if (!empty($this->responseErrors)) {
            // has errors, return
            return $this->responseErrors;
        }

        // has no errors, return default error body
        return [
            [
                'code' => $this->getStatusCode(),
                'message' => 'something went wrong.',
            ],
        ];
    }

    /**
     * get response body for json
     * @return \array[][]
     */
    public function getResponseBody()
    {
        return [
          'errors' =>  $this->getErrors(),
        ];
    }

    /**
     * handle json serialization for response
     * @inheritDoc
     */
    public function jsonSerialize(): mixed
    {
        return $this->getResponseBody();
    }
}
