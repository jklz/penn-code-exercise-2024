<?php

namespace App\Exceptions\Http;

use App\Exceptions\Http\HttpException;

class FailedToCreateResourceHttpException extends HttpException
{

    public function __construct(string $failedResource = 'Resource')
    {
        parent::__construct([
            [
                'code' => self::STATUS_UNPROCESSABLE_ENTITY,
                'message' => $failedResource . ' is not created.'
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return self::STATUS_UNPROCESSABLE_ENTITY;
    }
}