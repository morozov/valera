<?php

namespace Valera\Tests\Result;

use Valera\Result\Success;

/**
 * @covers \Valera\Result\Success
 */
class SuccessTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function dataIsStored()
    {
        $data = array('foo' => 'bar');
        $success = new Success($data);
        $this->assertEquals($data, $success->getData());
    }
}
