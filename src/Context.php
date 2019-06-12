<?php

namespace openWebX\Ruling;

use openWebX\Ruling\Exception\InvalidContextException;

/**
 * Class Context
 * @package openWebX\Ruling
 */
class Context {
    /**
     * @var array|mixed
     */
    private $context = [];

    /**
     * Context constructor.
     * @param $context
     * @throws InvalidContextException
     */
    public function __construct(?array $context) {
        if (!$this->valid($context)) {
            throw new InvalidContextException('Context must be an array with string keys and values.');
        }

        $this->context = $this->build($context);
    }

    /**
     * @return array|mixed
     */
    public function get() {
        return $this->context;
    }

    /**
     * @param $context
     * @return bool
     */
    public function valid(?array $context): bool {
        if (empty($context) || !is_array($context)) {
            return false;
        }

        foreach ($context as $key => $value) {
            if (empty($key) || !preg_match('/^[a-zA-Z\_]+$/', $key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $context
     * @return mixed
     */
    private function build(array $context) {
        foreach ($context as $key => $value) {
            $context[':' . $key] = $this->processValue($value);
            unset($context[$key]);
        }

        return $context;
    }

    /**
     * @param $value
     * @return bool|string
     */
    private function processValue($value) {
        if (!is_bool($value) && empty($value)) {
            return 'null';
        }

        $value = is_callable($value) ? $value() : $value;

        if (is_array($value)) {
            $value = '[' . implode(',', array_map(function ($e) {
                    return is_string($e) ? '"' . $e . '"' : $e;
                }, $value)) . ']';
        } elseif (is_string($value)) {
            $value = '"' . $value . '"';
        } elseif (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }

        return $value;
    }
}