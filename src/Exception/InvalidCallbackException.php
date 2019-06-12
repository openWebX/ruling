<?php

namespace openWebX\Ruling\Exception;

use Exception;

/**
 * Class InvalidCallbackException
 * @package openWebX\Ruling\Exception
 */
class InvalidCallbackException extends Exception {
    /**
     * @var string
     */
    protected $message = 'Invalid or uncallable callback.';
}