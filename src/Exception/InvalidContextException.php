<?php

namespace openWebX\Ruling\Exception;

use Exception;

/**
 * Class InvalidContextException
 * @package openWebX\Ruling\Exception
 */
class InvalidContextException extends Exception {
    /**
     * @var string
     */
    protected $message = 'Invalid context.';
}