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
    private $result;
    
    protected function setUp()
    {
        $this->result = new Result();
    }

    /** @test */
    public function resolve()
    {
        $this->result->resolve();
        $this->assertTrue($this->result->getStatus());
    }

    /** @test */
    public function fail1()
    {
        $this->result->fail('Failure reason');
        $this->assertFalse($this->result->getStatus());
        $this->assertEquals('Failure reason', $this->result->getMessage());
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function ensureSuccess()
    {
        $this->result->fail();
        $rm = new \ReflectionMethod($this->result, 'ensureSuccess');
        $rm->setAccessible(true);
        $rm->invoke($this->result);
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function successMessage()
    {
        $this->result->resolve();
        $this->result->getMessage();
    }

    /** @test */
    public function defaultFailed()
    {
        $this->assertFalse($this->result->getStatus());
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function resolveThenFail()
    {
        $this->result->resolve();
        $this->result->fail();
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function failThenResolve()
    {
        $this->result->fail();
        $this->result->resolve();
    }
}
