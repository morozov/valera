<?php

namespace Valera\Tests\Parser\Result;

use Valera\Blob;
use Valera\Document;
use Valera\Parser\Result\Success;
use Valera\Queue;
use Valera\Resource;
use Valera\Source;
use Valera\Storage\BlobStorage;
use Valera\Storage\DocumentStorage;

/**
 * @covers \Valera\Parser\Result\Success
 */
class SuccessTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function addDocument()
    {
        $storage = $this->getDocumentStorage();
        $storage->expects($this->once())
            ->method('create')
            ->with(1, array(
                'foo' => 'bar',
            ));

        $success = $this->getSuccess($storage, null, null);
        $success->addDocument(1, array(
            'foo' => 'bar',
        ));
    }

    /** @test */
    public function updateDocument()
    {
        $storage = $this->getDocumentStorage();
        $storage->expects($this->any())
            ->method('retrieve')
            ->will($this->returnValue(array(
                'foo' => 'bar',
            )));
        $storage->expects($this->once())
            ->method('update')
            ->with(2, array(
                'foo' => 'bar',
                'baz' => 'qux',
            ));

        $success = $this->getSuccess($storage, null, null);
        $success->updateDocument(2, function ($data) {
            return array_merge($data, array(
                'baz' => 'qux',
            ));
        });
    }

    /** @test */
    public function addBlob()
    {
        $resource = new Resource('http://example.com');

        $storage = $this->getBlobStorage();
        $storage->expects($this->once())
            ->method('create')
            ->with($resource, 'the-data');

        $success = $this->getSuccess(null, $storage, null);
        $success->addBlob($resource, 'the-data');
    }

    /** @test */
    public function addSource()
    {
        $queue = $this->getQueue();
        $queue->expects($this->once())
            ->method('enqueue');

        $success = $this->getSuccess(null, null, $queue);
        $referrer = new Resource('http://example.com');
        $success->addSource('product', 'http://example1.com', $referrer);
    }

    private function getSuccess(
        DocumentStorage $documentStorage = null,
        BlobStorage $blobStorage = null,
        Queue $queue = null
    ) {
        if (!$documentStorage) {
            $documentStorage = $this->getDocumentStorage();
        }

        if (!$blobStorage) {
            $blobStorage = $this->getBlobStorage();
        }

        if (!$queue) {
            $queue = $this->getQueue();
        }

        return new Success($documentStorage, $blobStorage, $queue);
    }

    /** @test */
    public function findBlobs()
    {
        $image1 = new Blob('http://example.com');
        $image2 = new Blob('http://example.com');
        $document = array(
            'image' => $image1,
            'some' => array(
                'deeply' => array(
                    'nested' => array(
                        'image' => $image2,
                    ),
                ),
            ),
        );

        $success = $this->getSuccess(null, null, null);
        $reflector = new \ReflectionObject($success);
        $findBlobs = $reflector->getMethod('findBlobs');
        $findBlobs->setAccessible(true);
        $blobs = $findBlobs->invokeArgs($success, array($document));

        $this->assertContains($image1, $blobs);
        $this->assertContains($image2, $blobs);
    }

    private function getDocumentStorage()
    {
        return $this->getMockBuilder('Valera\Storage\DocumentStorage')
            ->getMock();
    }

    private function getBlobStorage()
    {
        return $this->getMockBuilder('Valera\Storage\BlobStorage')
            ->getMock();
    }

    private function getQueue()
    {
        return $this->getMockBuilder('Valera\Queue')
            ->getMock();
    }
}
