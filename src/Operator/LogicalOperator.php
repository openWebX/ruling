<?php

namespace openWebX\Ruling\Operator;

/**
 * Class LogicalOperator
 * @package openWebX\Ruling\Operator
 */
class LogicalOperator {
    /**
     * @return array
     */
    public function all(): array {
        return [
            ' and ' => ' && ',
            ' or ' => ' || '
        ];
    }
}