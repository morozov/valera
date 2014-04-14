<?php

namespace Valera\Tests;

use Valera\Blob;
use Valera\Document;

/**
 * @covers \Valera\Loader
 */
class DocumentTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function getBlobs()
    {
        $image1 = new Blob('http://example.com');
        $image2 = new Blob('http://example.com');
        $document = new Document('1', array(
            'image' => $image1,
            'some' => array(
                'deeply' => array(
                    'nested' => array(
                        'image' => $image2,
                    ),
                ),
            ),
        ));

        $blobs = $document->getBlobs();
        $this->assertContains($image1, $blobs);
        $this->assertContains($image2, $blobs);
    }
}
