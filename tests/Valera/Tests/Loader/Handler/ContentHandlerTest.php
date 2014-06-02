<?php

namespace Valera\Tests\Loader\Handler;

use Valera\Loader\Handler\ContentHandler as Handler;
use Valera\Loader\Result;
use Valera\Tests\Value\Helper;

/**
 * @covers \Valera\Loader\Handler\ContentHandler
 * @uses \Valera\Content
 * @uses \Valera\Loader\Result
 * @uses \Valera\Resource
 * @uses \Valera\Source
 * @uses \Valera\Worker\Result
 */
class ContentHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Valera\Queue
     */
    private $contentQueue;

    /**
     * @var \Valera\Loader\Handler\ContentHandler
     */
    private $handler;

    /**
     * @var \Valera\Content
     */
    private $content;

    protected function setUp()
    {
        $logger = $this->getMock('Psr\\Log\\LoggerInterface');
        $this->contentQueue = $this->getMock('Valera\\Queue');
        $this->handler = new Handler($this->contentQueue, $logger);
        $this->content = null;
    }

    /** @test */
    public function handle()
    {
        $source = Helper::getSource();
        $result = new Result();
        $result->setContent('<p>Hello world!</p>', 'text/html');
        $this->contentQueue->expects($this->once())
            ->method('enqueue')
            ->will($this->returnCallback(function ($content) {
                $this->content = $content;
            }));
        $this->handler->handle($source, $result);

        $this->assertInstanceOf('Valera\\Content', $this->content);
        $this->assertEquals('<p>Hello world!</p>', $this->content->getContent());
        $this->assertEquals('text/html', $this->content->getMimeType());
        $this->assertEquals($source, $this->content->getSource());
    }
}
