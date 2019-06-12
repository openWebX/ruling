<?php

namespace openWebX\Ruling\Callback;

/**
 * Class SuccessCallback
 * @package openWebX\Ruling\Callback
 */
class SuccessCallback extends BaseCallback {
    /**
     * @return bool
     */
    protected function defaultCallback() {
        return true;
    }
}