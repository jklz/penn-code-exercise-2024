<?php

namespace App\ViewServices\Exception;

class ResourceNotFoundException extends \Exception
{
    /**
     * @var int
     */
    protected $code = 404;

    /**
     * @var string
     */
    protected $message = 'Not found.';
}