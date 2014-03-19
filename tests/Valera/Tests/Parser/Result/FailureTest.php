<?php

namespace Valera\Tests\Parser\Result;

use Valera\Parser\Result\Failure;

/**
 * @covers \Valera\Parser\Result\Failure
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
    public function testNonStringMessageIsRejected()
    {
        new Failure(null);
    }
}
