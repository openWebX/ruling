<?php

namespace openWebX\Ruling\Callback;

use openWebX\Ruling\Exception\InvalidCallbackException;

/**
 * Class BaseCallback
 * @package openWebX\Ruling\Callback
 */
abstract class BaseCallback {
    /**
     * @var callable|null
     */
    private $callback;

    /**
     * @return mixed
     */
    abstract protected function defaultCallback();

    /**
     * BaseCallback constructor.
     * @param null $callback
     * @throws InvalidCallbackException
     */
    public function __construct($callback = null) {
        if (is_null($this->defaultCallback())) {
            throw new InvalidCallbackException('Invalid default callback.');
        }

        if ($callback !== null && !is_callable($callback)) {
            throw new InvalidCallbackException('Callback must be callable.');
        }

        $this->callback = $callback;
    }

    /**
     * @return mixed
     */
    public function call() {
        return is_callable($this->callback) ? call_user_func($this->callback) : $this->defaultCallback();
    }
}