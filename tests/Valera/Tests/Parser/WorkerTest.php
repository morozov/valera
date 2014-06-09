<?php

namespace Valera\Tests\Parser;

use Valera\Parser\Worker;
use Valera\Tests\Value\Helper;

/**
 * @covers \Valera\Parser\Worker
 * @uses \Valera\Content
 * @uses \Valera\Resource
 * @uses \Valera\Value\ResourceData
 * @uses \Valera\Source
 * @uses \Valera\Source\DocumentSource
 */
class WorkerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Valera\Parser\ParserInterface
     */
    private $parser;

    /**
     * @var \Valera\Parser\Worker
     */
    private $worker;

    protected function setUp()
    {
        $logger = $this->getMock('Psr\\Log\\LoggerInterface');
        $this->parser = $this->getMock('Valera\\Parser\\ParserInterface');
        $this->worker = new Worker($this->parser, $logger);
    }

    /** @test */
    public function process()
    {
        $content = Helper::getContent();
        $result = $this->getMock('Valera\\Parser\\Result');
        $this->parser->expects($this->once())
            ->method('parse')
            ->with($content, $result);
        $this->worker->process($content, $result);
    }
}
