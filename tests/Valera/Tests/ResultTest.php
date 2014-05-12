<?php

namespace Valera\Tests;

use Valera\Result;

/**
 * @covers \Valera\Result
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Valera\Result
     */
    private $theResult;

    protected function setUp()
    {
        $this->theResult = new Result();
    }

    /** @test */
    public function resolve()
    {
        $this->theResult->resolve();
        $this->assertTrue($this->theResult->getStatus());
    }

    /** @test */
    public function fail1()
    {
        $this->theResult->fail('Failure reason');
        $this->assertFalse($this->theResult->getStatus());
        $this->assertEquals('Failure reason', $this->theResult->getReason());
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function ensureSuccess()
    {
        $this->theResult->fail('Failure reason');
        $rm = new \ReflectionMethod($this->theResult, 'ensureSuccess');
        $rm->setAccessible(true);
        $rm->invoke($this->theResult);
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function successMessage()
    {
        $this->theResult->resolve();
        $this->theResult->getReason();
    }

    /** @test */
    public function defaultFailed()
    {
        $this->assertFalse($this->theResult->getStatus());
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function resolveThenFail()
    {
        $this->theResult->resolve();
        $this->theResult->fail('Failure reason');
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function failThenResolve()
    {
        $this->theResult->fail('Failure reason');
        $this->theResult->resolve();
    }
}
