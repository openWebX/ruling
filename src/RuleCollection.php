<?php

namespace openWebX\Ruling;

use openWebX\Ruling\Exception\InvalidRuleException;

/**
 * Class RuleCollection
 * @package openWebX\Ruling
 */
class RuleCollection {
    /**
     * @var array
     */
    private $rules = [];

    /**
     * RuleCollection constructor.
     * @param $rules
     * @throws InvalidRuleException
     */
    public function __construct($rules) {
        if (!is_array($rules)) {
            $rules = [$rules];
        }

        if (!$this->valid($rules)) {
            throw new InvalidRuleException('Rule must be a string or an array of strings.');
        }

        $this->rules = $rules;
    }

    /**
     * @return array
     */
    public function get(): array {
        return $this->rules;
    }

    /**
     * @param $rules
     * @return bool
     */
    public function valid($rules): bool {
        foreach ($rules as $rule) {
            if (empty($rule) || !is_string($rule)) {
                return false;
            }
        }

        return true;
    }
}