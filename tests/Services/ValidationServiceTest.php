<?php

namespace AppTests\Services;

use App\Exceptions\Http\ValidationHttpException;
use App\Services\ValidationService;
use PHPUnit\Framework\TestCase;

class ValidationServiceTest extends TestCase
{

    public function testValidateToThrowExceptionWhenMissing()
    {
        // create instance of ValidationService
        $validationService = new ValidationService();

        // add field config
        $validationService->addFieldConfig('name', 'Name', 'string', true);

        // setup test to expect ValidationHttpException to be thrown
        $this->expectException(ValidationHttpException::class);

        // validate with bad data, verify that exception is thrown
        $validationService->validate([
            'email' => 'john@example.com',
        ]);

        // fail test if it reaches here
        $this->assertTrue(false);
    }

    public function testValidateToThrowExceptionWrongType()
    {
        // create instance of ValidationService
        $validationService = new ValidationService();

        // add field config
        $validationService->addFieldConfig('name', 'Name', 'string', true);

        // validate with bad data, verify that exception is thrown
        $this->expectException(ValidationHttpException::class);

        // validate with bad data, verify that exception is thrown
        $validationService->validate([
            'name' => 12,
            'email' => 'john@example.com',
        ]);

        // fail test if it reaches here
        $this->assertTrue(false);
    }

    public function testValidateWhenValid()
    {
        // create instance of ValidationService
        $validationService = new ValidationService();

        // add field config
        $validationService->addFieldConfig('name', 'Name', 'string', true);

        // validate with good data
        $validationService->validate([
            'name' => 'John',
            'email' => 'john@example.com',
        ]);

        // validation should be valid
        $this->assertTrue($validationService->isValid());

        // get values that passed validation
        $values = $validationService->getValidatedValues();

        // validated values should include key for name
        $this->assertArrayHasKey('name', $values);

        // should get the value from get safe
        $safeNameValue = $validationService->getSafeValue('name');

        // shouldn't be null
        $this->assertNotNull($safeNameValue);

        // should be a string
        $this->assertIsString($safeNameValue);

        // should equal to $values['name']
        $this->assertEquals($values['name'], $safeNameValue);
    }

    public function testIsValidWhenValid()
    {
        // create instance of ValidationService
        $validationService = new ValidationService();

        // add field config
        $validationService->addFieldConfig('name', 'Name', 'string', true);

        try {
            // validate with bad data
            $validationService->validate([
                'email' => 'john@example.com',
            ]);
        } catch (\Exception $exception) {
            // ignore Exception

        }

        // isValid should return false
        $this->assertFalse($validationService->isValid());
    }

    public function testIsValidWhenNotValid()
    {

        // create instance of ValidationService
        $validationService = new ValidationService();

        // add field config
        $validationService->addFieldConfig('name', 'Name', 'string', true);

        // validate with good data
        $validationService->validate([
            'name' => 'John',
        ]);

        $this->assertTrue($validationService->isValid());
    }
}
