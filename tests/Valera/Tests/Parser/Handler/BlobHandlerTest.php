<?php

namespace Valera\Tests\Parser\Handler;

use Valera\Parser\Handler\BlobHandler as Handler;
use Valera\Parser\Result;
use Valera\Tests\Value\Helper;

/**
 * @covers \Valera\Parser\Handler\BlobHandler
 * @uses \Valera\Blob
 * @uses \Valera\Content
 * @uses \Valera\Parser\Result
 * @uses \Valera\Resource
 * @uses \Valera\Source
 * @uses \Valera\Worker\Result
 */
class BlobHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Valera\Storage\BlobStorage|\PHPUnit_Framework_MockObject_MockObject
     */
    private $blobStorage;

    /**
     * @var \Valera\Storage\DocumentStorage|\PHPUnit_Framework_MockObject_MockObject
     */
    private $documentStorage;

    /**
     * @var \Valera\Parser\Handler\BlobHandler
     */
    private $handler;

    /**
     * @var \Valera\Blob
     */
    private $blob;

    protected function setUp()
    {
        $logger = $this->getMock('Psr\\Log\\LoggerInterface');
        $this->blobStorage = $this->getMock('Valera\\Storage\\BlobStorage');
        $this->documentStorage = $this->getMock('Valera\\Storage\\DocumentStorage');
        $this->handler = new Handler($this->blobStorage, $this->documentStorage, $logger);
        $this->blob = null;
    }

    /** @test */
    public function handle()
    {
        $content = Helper::getContent();
        $resource = Helper::getResource();
        $result = new Result();
        $result->addBlob($resource, 'blob-contents');
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

        $this->handler->handle($content, $result);

        $this->assertInstanceOf('Valera\\Blob', $this->blob);
        $this->assertEquals($resource, $this->blob->getResource());
        $this->assertEquals('/path/to/blob', $this->blob->getPath());
    }
}
