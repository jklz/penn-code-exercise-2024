<?php

namespace App\Controllers\Traits;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

trait RequestValidationTrait
{
    use JsonResponseTrait;
    protected array $validationErrors;

    /**
     * @var array<string, {name: string; type: "string"|"int"; isRequired: bool}>;
     *
     */
    protected array $validationConfig = [];

    protected array $validatedValues;

    private function addJsonFieldForValidation(string $field, string $name, string $type, bool $isRequired = false): void
    {
        $this->validationConfig[$field] = [
            'name' => $name,
            'type' => $type,
            'isRequired' => $isRequired,
        ];
    }

    private function validateConfiguredFieldValue(string $field, $value = null)
    {
        $config = $this->validationConfig[$field];
        $fieldName = $config['name'];
        $fieldType = $config['type'];


        if (is_null($value) && $config['isRequired']) {
            $this->validationErrors[] = [
                'field' => $field,
                'message' => $fieldName . ' is required',
            ];
            // return to exit
            return;
        }

        $isValueTypeValid = false;
        switch ($fieldType) {
            case 'string':
                $isValueTypeValid = is_string($value);
                break;
            case 'int':
                $isValueTypeValid = is_int($value);
                break;
            default:
        }

        if (!$isValueTypeValid) {
            // not valid value
            $this->validationErrors[] = [
                'field' => $field,
                'message' => $fieldName . ' is invalid',
            ];
            // return to exit
            return;
        }

        return $value;

    }

    private function validateJsonBodyValues(Request $request): void
    {
        // start with clear validation results
        $this->validatedValues = [];
        $this->validationErrors = [];


        $parsedJsonBody = $request->getParsedBody();

        // get fields to validate
        $fields = array_keys($this->validationConfig);

        foreach ($fields AS $field) {
            $parsedValue = $parsedJsonBody[$field] ?? null;
            $validatedValue = $this->validateConfiguredFieldValue($field, $parsedValue);

            // if we have a validated value, include for response
            if (isset($validatedValue)) {
                $this->validatedValues[$field] = $validatedValue;
            }
        }
    }

    private function isJsonBodyValidationSuccessful(Request $request = null): bool
    {
        if (!isset($this->validationErrors)) {
            // need to first validate
            $this->validateJsonBodyValues($request);
        }
        // if we have no validation errors validation is successful
        $isValidationSuccessful =  count($this->validationErrors) === 0;
        return $isValidationSuccessful;
    }

    private function errorJsonBodyValidationJsonResponse(\Slim\Psr7\Response $response = null): \Slim\Psr7\Response|\Psr\Http\Message\ResponseInterface
    {
        return $this->errorJsonResponse($this->validationErrors, StatusCodeInterface::STATUS_BAD_REQUEST);
    }

    private function getValidatedJsonBodyValues(Request $request = null): array
    {
        if (!isset($this->validatedValues)) {
            // need to first validate
            $this->validateJsonBodyValues($request);
        }

        return $this->validatedValues;
    }
}