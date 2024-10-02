<?php

namespace App\Controllers\Traits;

use App\Services\ValidationService;
use Psr\Http\Message\ServerRequestInterface as Request;

trait RequestValidationTrait
{
    protected ValidationService $validationService;

    /**
     * get passed json body and validate via validation service
     * @param Request $request
     * @return void
     * @throws \App\Exceptions\Http\ValidationHttpException
     */
    private function validateJsonBodyValues(Request $request): void
    {
        $parsedJsonBody = $request->getParsedBody();

        $this->validationService
            ->validate($parsedJsonBody);
    }
}