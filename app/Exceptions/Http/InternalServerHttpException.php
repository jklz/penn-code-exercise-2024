<?php

namespace App\Exceptions\Http;

class InternalServerHttpException extends HttpException
{
    public function __construct(string $message = 'Internal Server Error.', ?\Throwable $previous = null)
    {
        parent::__construct([], $previous);
        // add error
        $this->addError([
            'code' => self::STATUS_INTERNAL_SERVER_ERROR,
            'message' => $message,
        ]);
    }

    public function getStatusCode(): int
    {
        return self::STATUS_INTERNAL_SERVER_ERROR;
    }
}