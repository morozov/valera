<?php

namespace Valera\Tests\Entity;

use Valera\Entity\Document;
use Valera\Tests\Value\Helper;

/**
 * @covers Valera\Entity\Document
 * @uses Valera\Blob
 * @uses Valera\Resource
 */
class DocumentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function api()
    {
        $document = new Document('test', array('foo' => 'bar'));
        $this->assertEquals('test', $document->getId());
        $this->assertEquals(array(
            'foo' => 'bar',
        ), $document->getData());
        $this->assertFalse($document->isDirty());

        return $document;
    }

    /**
     * @test
     * @depends api
     */
    public function update(Document $document)
    {
        $document->update(function (array $data) {
            return array_merge($data, array(
                'baz' => 'qux',
            ));
        });

        $this->assertEquals(array(
            'foo' => 'bar',
            'baz' => 'qux',
        ), $document->getData());
        $this->assertTrue($document->isDirty());
    }

    /**
     * @test
     */
    public function getResources()
    {
        $r1 = Helper::getResource();
        $r2 = Helper::getAnotherResource();
        $document = new Document('test', array(
            'foo' => $r1,
            'bar' => array(
                'baz' => $r2,
            ),
        ));

        $this->assertEquals(array($r1, $r2), $document->getResources());
    }

    /**
     * @test
     */
    public function replaceResource()
    {
        $r1 = Helper::getResource();
        $b1 = Helper::getBlob();
        $document = new Document('test', array(
            'foo' => 'bar',
            'baz' => array(
                'qux' => $r1,
            ),
        ));
        $document->replaceResource($b1);

        $this->assertEquals(array(
            'foo' => 'bar',
            'baz' => array(
                'qux' => $b1,
            ),
        ), $document->getData());
        $this->assertTrue($document->isDirty());
    }
}
