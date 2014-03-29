<?php

namespace Valera\Tests\Result;

use Valera\Result\Failure;

/**
 * @covers \Valera\Result\Failure
 */
class FailureTest extends \PHPUnit_Framework_TestCase
{
    public function testMessageIsStored()
    {
        $failure = new Failure('Failure Test');
        $message = $failure->getMessage();
        $this->assertEquals('Failure Test', $message);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMessageOfWrongTypeIsRejected()
    {
        new Failure(array());
    }
}
