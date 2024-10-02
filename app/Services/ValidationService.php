<?php

namespace App\Services;

use App\Exceptions\Http\ValidationHttpException;

class ValidationService
{
    /**
     * @var array<string, {name: string; type: "string"|"int"; isRequired: bool}>;
     *
     */
    protected array $validationConfig = [];

    /**
     * Values that have been validated
     * @var array<string, mixed>
     */
    protected array $validatedValues;

    protected ValidationHttpException $validationHttpException;

    /**
     * get validation exception, creating new if one doesn't currently exist
     * @return ValidationHttpException
     */
    private function getValidationException(): ValidationHttpException
    {
        // ensure we have a validation exception
        $this->validationHttpException ??= new ValidationHttpException();

        return $this->validationHttpException;
    }

    /**
     * is validation valid
     * @return bool
     */
    public function isValid(): bool
    {
        return (
            // do not have validation exception
            !isset($this->validationHttpException)
            // OR have no errors in validation exception
            || count($this->validationHttpException->getErrors()) === 0
        );
    }

    /**
     * add configuration to use in validating field value
     * @param string $field
     * @param string $name
     * @param string $type
     * @param bool $isRequired
     * @return $this
     */
    public function addFieldConfig(string $field, string $name, string $type, bool $isRequired = false): self
    {
        $this->validationConfig[$field] = [
            'name' => $name,
            'type' => $type,
            'isRequired' => $isRequired,
        ];

        return $this;
    }

    private function addValidationError(string $field, string $validationMessage): void
    {
        $this->getValidationException()
            ->addFieldError($field, $validationMessage);
    }

    private function validateFieldValue(string $field, $value = null)
    {
        $config = $this->validationConfig[$field];

        $fieldName = $config['name'];
        $fieldType = $config['type'];


        if (is_null($value) && $config['isRequired']) {
            $this->addValidationError($field, $fieldName . ' is required.');
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
            $this->addValidationError($field, $fieldName . ' is invalid.');
            // return to exit
            return;
        }

        return $value;

    }

    /**
     * validate configured fields from included data
     * @param array $dataToValidate
     * @return void
     * @throws ValidationHttpException
     */
    public function validate(array $dataToValidate): void
    {
        // start with clear validation results
        $this->validatedValues = [];
        if (isset($this->validationHttpException)) {
            $this->validationHttpException = new ValidationHttpException();
        }

        // get fields to validate
        $fields = array_keys($this->validationConfig);

        // check each field
        foreach ($fields AS $field) {
            $fieldValue = $dataToValidate[$field] ?? null;
            $validatedValue = $this->validateFieldValue($field, $fieldValue);

            // if we have a validated value, include in validatedValues
            if (isset($validatedValue)) {
                $this->validatedValues[$field] = $validatedValue;
            }
        }

        if (!$this->isValid()) {
            throw $this->validationHttpException;
        }
    }

    public function getSafeValue(string $field): mixed
    {
        return $this->validatedValues[$field] ?? null;
    }

    /**
     * get values that have been validated
     * @return array
     */
    public function getValidatedValues(): array
    {
        return $this->validatedValues;
    }

}