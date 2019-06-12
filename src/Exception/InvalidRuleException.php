<?php

namespace openWebX\Ruling\Exception;

use Exception;

/**
 * Class InvalidRuleException
 * @package openWebX\Ruling\Exception
 */
class InvalidRuleException extends Exception {
    /**
     * @var string
     */
    protected $message = 'Invalid rule.';
}