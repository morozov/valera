<?php

namespace Valera\Tests\Parser\Handler;

use Valera\Parser\Handler\SourceHandler as Handler;
use Valera\Parser\Result;
use Valera\Resource;
use Valera\Tests\Value\Helper;

/**
 * @covers \Valera\Parser\Handler\SourceHandler
 * @uses \Valera\Content
 * @uses \Valera\Parser\Result
 * @uses \Valera\Resource
 * @uses \Valera\Source
 * @uses \Valera\Worker\Result
 */
class SourceHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Valera\Queue|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sourceQueue;

    /**
     * @var \Valera\Parser\Handler\SourceHandler
     */
    private $handler;

    /**
     * @var \Valera\Source
     */
    private $source;

    protected function setUp()
    {
        $logger = $this->getMock('Psr\\Log\\LoggerInterface');
        $this->sourceQueue = $this->getMock('Valera\\Queue');
        $this->handler = new Handler($this->sourceQueue, $logger);
        $this->source = null;
    }

    /** @test */
    public function handle()
    {
        $result = new Result();
        $result->addSource(
            'test-type',
            'http://example.com/',
            Resource::METHOD_POST,
            array('Content-Type' => 'application/octet-stream'),
            'request-body'
        );

        $this->sourceQueue->expects($this->once())
            ->method('enqueue')
            ->will($this->returnCallback(function ($source) {
                $this->source = $source;
            }));

        $content = Helper::getContent();
        $this->handler->handle($content, $result);

        $this->assertInstanceOf('Valera\\Source', $this->source);
        $this->assertEquals('test-type', $this->source->getType());

        $resource = $this->source->getResource();
        $this->assertEquals(Resource::METHOD_POST, $resource->getMethod());
        $this->assertEquals(array(
            'Content-Type' => 'application/octet-stream',
        ), $resource->getHeaders());
        $this->assertEquals('request-body', $resource->getData());
        $this->assertEquals($content->getResource()->getUrl(), $resource->getReferrer());
    }
}
