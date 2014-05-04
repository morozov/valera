<?php

namespace Valera\Tests\Worker;

use Valera\Blob;
use Valera\DocumentIterator;
use Valera\Resource;

class DocumentIteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Valera\DocumentIterator
     */
    private $iterator;

    protected function setUp()
    {
        $this->iterator = new DocumentIterator();
    }

    /** @test */
    public function findBlobs()
    {
        $image1 = new Resource('http://example.com');
        $image2 = new Resource('http://example.com');
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

        $blobs = $this->iterator->findEmbedded($document);

        $this->assertContains($image1->getHash(), $blobs);
        $this->assertContains($image2->getHash(), $blobs);
    }

    /** @test */
    public function convertBlob()
    {
        $resource = new Resource('http://example.com');
        $document = array(
            'image' => $resource,
        );

        $this->iterator->convertEmbedded($document, $resource, '/path/to/image');

        $this->assertEquals(array(
            'image' => new Blob($resource, '/path/to/image'),
        ), $document);
    }
}
