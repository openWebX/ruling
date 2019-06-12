<?php

namespace subzeta\Ruling\Test\Callback;

use PHPUnit_Framework_TestCase;
use subzeta\Ruling\Callback\SuccessCallback;

class SuccessCallbackTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function itShouldReturnAsItsDefaultCallback()
    {
        $this->assertSame(true, (new SuccessCallback())->call());
    }
}