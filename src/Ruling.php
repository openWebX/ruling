<?php

namespace openWebX\Ruling;

use openWebX\Ruling\Callback\FailCallback;
use openWebX\Ruling\Callback\SuccessCallback;
use openWebX\Ruling\Evaluator\Evaluator;

/**
 * Class Ruling
 * @package openWebX\Ruling
 */
class Ruling {
    /** @var Context */
    private $context;

    /** @var RuleCollection */
    private $rules;

    /** @var Callback */
    private $successCallback;

    /** @var Callback */
    private $failCallback;

    /** @var Evaluator */
    private $evaluator;

    /**
     * Ruling constructor.
     */
    public function __construct() {
        $this->evaluator = new Evaluator();
    }

    /**
     * @param $context
     * @return Ruling
     * @throws Exception\InvalidContextException
     */
    public function given($context): self {
        $this->context = new Context($context);

        return $this;
    }

    /**
     * @param $rules
     * @return Ruling
     * @throws Exception\InvalidRuleException
     */
    public function when($rules): self {
        $this->rules = new RuleCollection($rules);

        return $this;
    }

    /**
     * @param $callback
     * @return Ruling
     * @throws Exception\InvalidCallbackException
     */
    public function then($callback): self {
        $this->successCallback = new SuccessCallback($callback);

        return $this;
    }

    /**
     * @param $callback
     * @return Ruling
     * @throws Exception\InvalidCallbackException
     */
    public function otherwise($callback): self {
        $this->failCallback = new FailCallback($callback);

        return $this;
    }

    /**
     * @return mixed
     * @throws Exception\InvalidRuleException
     */
    public function execute() {
        return $this->evaluator->assert($this->rules, $this->context) ?
            $this->success()->call() :
            $this->fail()->call();
    }

    /**
     * @return array
     */
    public function interpret(): array {
        return $this->evaluator->interpret($this->rules, $this->context);
    }

    /**
     * @return SuccessCallback|null
     * @throws Exception\InvalidCallbackException
     */
    private function success(): ?SuccessCallback {
        return $this->successCallback ?? new SuccessCallback();
    }

    /**
     * @return FailCallback|null
     * @throws Exception\InvalidCallbackException
     */
    private function fail(): ?FailCallback {
        return $this->failCallback ?? new FailCallback();
    }
}
