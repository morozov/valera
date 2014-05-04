<?php

namespace Valera\Tests\Loader;

use Valera\Loader\Result;

/**
 * @covers \Valera\Loader\Result
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Valera\Loader\Result
     */
    private $result;

    protected function setUp()
    {
        $this->result = new Result();
    }

    /** @test */
    public function defaults()
    {
        $this->result->resolve();
        $this->assertNull($this->result->getContent());
    }

    /**
     * @test
     * @depends defaults
     */
    public function setContent()
    {
        $this->result->setContent('content');
        $content = $this->result->getContent();

        $this->assertEquals('content', $content);
        $this->assertTrue($this->result->getStatus());
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function setcontentDuplicate()
    {
        $this->result->setContent('content1');
        $this->result->setContent('content2');
    }
}
