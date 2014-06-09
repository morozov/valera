<?php

namespace Valera\Tests\Parser\Handler;

use Valera\Entity\Document;
use Valera\Parser\Handler\DocumentHandler as Handler;
use Valera\Parser\Result;
use Valera\Resource;
use Valera\Tests\Value\Helper;

/**
 * @covers \Valera\Parser\Handler\DocumentHandler
 * @uses \Valera\Content
 * @uses \Valera\Entity\Document
 * @uses \Valera\Parser\Result
 * @uses \Valera\Resource
 * @uses \Valera\Value\ResourceData
 * @uses \Valera\Source
 * @uses \Valera\Source\DocumentSource
 * @uses \Valera\Worker\Result
 */
class DocumentHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Valera\Storage\DocumentStorage|\PHPUnit_Framework_MockObject_MockObject
     */
    private $documentStorage;

    /**
     * @var \Valera\Queue|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sourceQueue;

    /**
     * @var \Valera\Parser\Handler\DocumentHandler
     */
    private $handler;

    /**
     * @var \Valera\Parser\PostProcessor
     */
    private $postProcessor;

    /**
     * @var \Valera\Entity\Document
     */
    private $document;

    /**
     * @var \Valera\Source
     */
    private $source;

    protected function setUp()
    {
        $logger = $this->getMock('Psr\\Log\\LoggerInterface');
        $this->documentStorage = $this->getMock('Valera\\Storage\\DocumentStorage');
        $this->sourceQueue = $this->getMock('Valera\\Queue');
        $this->postProcessor = $this->getMock('Valera\\Parser\\PostProcessor');
        $this->handler = new Handler($this->documentStorage, $this->sourceQueue, array($this->postProcessor), $logger);
        $this->document = null;
        $this->source = null;
    }

    /** @test */
    public function createDocument()
    {
        $result = new Result();
        $resource = Helper::getResource();
        $id = 'test-create';
        $data = array(
            'foo' => 'bar',
            'baz' => $resource,
        );
        $result->addDocument($id, $data);

        $this->documentStorage->expects($this->once())
            ->method('create')
            ->will($this->returnCallback(function ($document) {
                $this->document = $document;
            }));

        $this->setUpSourceQueue();

        $content = Helper::getContent();
        $this->handler->handle($content, $result);

        $this->assertInstanceOf('Valera\\Entity\\Document', $this->document);
        $this->assertEquals($id, $this->document->getId());
        $this->assertEquals($data, $this->document->getData());

        $this->assertEnqueuedSource($resource);
    }

    /** @test */
    public function updateDocument()
    {
        $result = new Result();
        $resource = Helper::getAnotherResource();
        $id = 'test-update';
        $callback = function () {
        };
        $result->updateDocument($id, $callback);

        $document = $this->getMockBuilder('Valera\\Entity\\Document')
            ->disableOriginalConstructor()
            ->getMock();
        $document->expects($this->once())
            ->method('update')
            ->with($callback);
        $document->expects($this->once())
            ->method('getResources')
            ->will($this->returnValue(array($resource)));

        $this->documentStorage->expects($this->once())
            ->method('retrieve')
            ->with($id)
            ->will($this->returnValue($document));

        $this->setUpSourceQueue();
        $this->setPostProcessor($document);

        $content = Helper::getContent();
        $this->handler->handle($content, $result);

        $this->assertEnqueuedSource($resource);
    }

    private function setUpSourceQueue()
    {
        $this->sourceQueue->expects($this->once())
            ->method('enqueue')
            ->will($this->returnCallback(function ($source) {
                $this->source = $source;
            }));
    }

    private function setPostProcessor(Document $document)
    {
        $this->postProcessor->expects($this->once())
            ->method('process')
            ->with($document);
    }

    private function assertEnqueuedSource(Resource $resource)
    {
        $this->assertInstanceOf('Valera\\Source\\BlobSource', $this->source);
        $this->assertEquals($resource, $this->source->getResource());
    }
}
