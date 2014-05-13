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
    private $theResult;

    protected function setUp()
    {
        $this->theResult = new Result();
    }

    /** @test */
    public function defaults()
    {
        $this->theResult->resolve();
        $this->assertNull($this->theResult->getContent());
    }

    /**
     * @test
     * @depends defaults
     */
    public function setContent()
    {
        $this->theResult->setContent('content', 'text/plain');
        $content = $this->theResult->getContent();
        $mimeType = $this->theResult->getMimeType();

        $this->assertEquals('content', $content);
        $this->assertEquals('text/plain', $mimeType);
        $this->assertTrue($this->theResult->getStatus());
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function setContentDuplicate()
    {
        $this->theResult->setContent('content1', null);
        $this->theResult->setContent('content2', null);
    }
}
