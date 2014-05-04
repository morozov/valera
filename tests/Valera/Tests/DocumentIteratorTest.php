<?php

namespace Valera\Tests\Worker;

use Valera\Blob\Local as LocalBlob;
use Valera\Blob\Remote as RemoteBlob;
use Valera\DocumentIterator;

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
        $image1 = new RemoteBlob('http://example.com');
        $image2 = new RemoteBlob('http://example.com');
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

        $blobs = $this->iterator->findBlobs($document);

        $this->assertContains($image1->getHash(), $blobs);
        $this->assertContains($image2->getHash(), $blobs);
    }

    /** @test */
    public function convertBlob()
    {
        $remoteBlob = new RemoteBlob('http://example.com');
        $hash = $remoteBlob->getHash();
        $document = array(
            'image' => $remoteBlob,
        );

        $this->iterator->convertBlob($document, $hash, '/path/to/image');

        $this->assertEquals(array(
            'image' => new LocalBlob('/path/to/image'),
        ), $document);
    }
}
