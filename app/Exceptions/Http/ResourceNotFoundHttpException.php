<?php

namespace App\Exceptions\Http;


class ResourceNotFoundHttpException extends HttpException
{
    public function __construct(string $resource = 'resource')
    {

        parent::__construct([
            'code' => self::STATUS_NOT_FOUND,
            'message' => $resource . ' not found.'
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return self::STATUS_NOT_FOUND;
    }
}