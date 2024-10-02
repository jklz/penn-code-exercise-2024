<?php

namespace App\Exceptions\Http;

class ValidationHttpException extends HttpException
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return self::STATUS_BAD_REQUEST;
    }

    public function addFieldError(string $field, string $message): void
    {
        $this->addError([
            'field' => $field,
            'message' => $message,
        ]);
    }
}