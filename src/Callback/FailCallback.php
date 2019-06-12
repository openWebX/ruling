<?php

namespace openWebX\Ruling\Callback;

/**
 * Class FailCallback
 * @package openWebX\Ruling\Callback
 */
class FailCallback extends BaseCallback {
    /**
     * @return bool
     */
    protected function defaultCallback() {
        return false;
    }
}