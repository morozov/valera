<?php

namespace Valera\Tests\Loader\Handler;

use Valera\Loader\Handler\ContentHandler as Handler;
use Valera\Loader\Result;
use Valera\Tests\Value\Helper;

/**
 * @covers \Valera\Loader\Handler\ContentHandler
 * @uses \Valera\Blob
 * @uses \Valera\Content
 * @uses \Valera\Loader\Result
 * @uses \Valera\Resource
 * @uses \Valera\Source
 * @uses \Valera\Source\DocumentSource
 * @uses \Valera\Worker\Result
 */
class ContentHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Valera\Queue|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contentQueue;

    /**
     * @var \Valera\Storage\BlobStorage|\PHPUnit_Framework_MockObject_MockObject
     */
    private $blobStorage;

    /**
     * @var \Valera\Storage\DocumentStorage|\PHPUnit_Framework_MockObject_MockObject
     */
    private $documentStorage;

    /**
     * @var \Valera\Loader\Handler\ContentHandler
     */
    private $handler;

    /**
     * @var \Valera\Content
     */
    private $content;

    /**
     * @var \Valera\Blob
     */
    private $blob;

    protected function setUp()
    {
        $logger = $this->getMock('Psr\\Log\\LoggerInterface');
        $this->contentQueue = $this->getMock('Valera\\Queue');
        $this->blobStorage = $this->getMock('Valera\\Storage\\BlobStorage');
        $this->documentStorage = $this->getMock('Valera\\Storage\\DocumentStorage');
        $this->handler = new Handler(
            $this->contentQueue,
            $this->blobStorage,
            $this->documentStorage,
            $logger
        );
        $this->blob = null;
        $this->content = null;
    }

    /** @test */
    public function handleDocumentContents()
    {
        $source = Helper::getDocumentSource();
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

    /** @test */
    public function handleBlobContents()
    {
        $source = Helper::getBlobSource();
        $resource = Helper::getResource();
        $result = new Result();
        $result->setContent('blob-contents', 'image/png');
        $this->blobStorage->expects($this->once())
            ->method('create')
            ->with($resource, 'blob-contents')
            ->will(
                $this->returnValue('/path/to/blob')
            );

        $document = $this->getMockBuilder('Valera\\Entity\\Document')
            ->disableOriginalConstructor()
            ->getMock();
        $document->expects($this->once())
            ->method('replaceResource')
            ->will($this->returnCallback(function ($blob) {
                $this->blob = $blob;
            }));

        $this->documentStorage->expects($this->once())
            ->method('findByResource')
            ->with($resource)
            ->will(
                $this->returnValue(array($document))
            );

        $this->handler->handle($source, $result);

        $this->assertInstanceOf('Valera\\Blob', $this->blob);
        $this->assertEquals($resource, $this->blob->getResource());
        $this->assertEquals('/path/to/blob', $this->blob->getPath());
    }
}
