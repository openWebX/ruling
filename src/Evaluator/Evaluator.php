<?php

namespace openWebX\Ruling\Evaluator;

use nicoSWD\Rules\Rule;
use openWebX\Ruling\Context;
use openWebX\Ruling\Exception\InvalidRuleException;
use openWebX\Ruling\Operator\ComparisonOperator;
use openWebX\Ruling\Operator\LogicalOperator;
use openWebX\Ruling\RuleCollection;

/**
 * Class Evaluator
 * @package openWebX\Ruling\Evaluator
 */
class Evaluator {
    /**
     * @param RuleCollection $rules
     * @param Context $context
     * @return bool
     * @throws InvalidRuleException
     */
    public function assert(RuleCollection $rules, Context $context): bool {
        if (!$this->valid($rules, $context)) {
            throw new InvalidRuleException(
                'Rules aren\'t semantically valid (' . implode(',', $this->build($rules, $context)) . ').'
            );
        }

        return array_product(
            array_map(
                function ($rule) {
                    return (new Rule($rule))->isTrue();
                },
                $this->interpret($rules, $context)
            )
        );
    }

    /**
     * @param RuleCollection $rules
     * @param Context $context
     * @return array
     */
    public function interpret(RuleCollection $rules, Context $context): array {
        return $this->build($rules, $context);
    }

    /**
     * @param RuleCollection $rules
     * @param Context $context
     * @return bool
     */
    private function valid(RuleCollection $rules, Context $context): bool {
        return array_product(
            array_map(
                function ($rule) {
                    return (new Rule($rule))->isValid();
                },
                $this->interpret($rules, $context)
            )
        );
    }

    /**
     * @param RuleCollection $rules
     * @param Context $context
     * @return array
     */
    private function build(RuleCollection $rules, Context $context): array {
        return array_map(
            function ($rule) use ($context) {
                return $this->prepare($rule, $context);
            },
            $rules->get()
        );
    }

    /**
     * @param string $rule
     * @param Context $context
     * @return string
     */
    private function prepare(string $rule, Context $context): string {
        $replacements = array_merge(
            (new ComparisonOperator())->all(),
            (new LogicalOperator())->all(),
            $context->get()
        );

        return str_replace(array_keys($replacements), array_values($replacements), $rule);
    }
}
