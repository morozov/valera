<?php

namespace Valera\Tests\Parser;

use Valera\Parser\Result;
use Valera\Resource;
use Valera\Tests\Value\Helper;

/**
 * @covers \Valera\Parser\Result
 * @covers \Valera\Worker\Result
 * @uses \Valera\Resource
 * @uses \Valera\Value\ResourceData
 * @uses \Valera\Worker\Result
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Valera\Parser\Result
     */
    private $theResult;

    protected function setUp()
    {
        $this->theResult = new Result();
    }

    /** @test */
    public function defaults()
    {
        $this->theResult->resolve();
        $this->assertEmpty($this->theResult->getNewDocuments());
        $this->assertEmpty($this->theResult->getUpdatedDocuments());
        $this->assertEmpty($this->theResult->getBlobs());
        $this->assertEmpty($this->theResult->getSources());
    }

    /**
     * @test
     * @depends defaults
     */
    public function addDocument()
    {
        $this->theResult->addDocument('test1', array('foo1' => 'bar1'));
        $this->theResult->addDocument('test2', array('foo2' => 'bar2'));
        $newDocuments = $this->theResult->getNewDocuments();

        $this->assertEquals(array(
            'test1' => array('foo1' => 'bar1'),
            'test2' => array('foo2' => 'bar2'),
        ), $newDocuments);
        $this->assertTrue($this->theResult->getStatus());
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function addDocumentDuplicate()
    {
        $this->theResult->addDocument('test', array('foo1' => 'bar1'));
        $this->theResult->addDocument('test', array('foo2' => 'bar2'));
    }

    /**
     * @test
     * @depends defaults
     */
    public function updateDocument()
    {
        $callback1 = function () {
        };
        $callback2 = function () {
        };
        
        $this->theResult->updateDocument('test1', $callback1);
        $this->theResult->updateDocument('test2', $callback2);
        $updatedDocuments = $this->theResult->getUpdatedDocuments();

        $this->assertEquals(array(
            'test1' => $callback1,
            'test2' => $callback1,
        ), $updatedDocuments);
        $this->assertTrue($this->theResult->getStatus());
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function updateDocumentDuplicate()
    {
        $callback1 = function () {
        };
        $callback2 = function () {
        };

        $this->theResult->updateDocument('test', $callback1);
        $this->theResult->updateDocument('test', $callback2);
    }

    /**
     * @test
     * @depends defaults
     */
    public function addBlob()
    {
        $resource = Helper::getResource();
        $this->theResult->addBlob($resource, 'contents');
        $blobs = $this->theResult->getBlobs();

        $this->assertEquals(array(
            array(
                $resource,
                'contents',
            ),
        ), $blobs);
        $this->assertTrue($this->theResult->getStatus());
    }

    /**
     * @test
     * @depends defaults
     */
    public function addSource()
    {
        $this->theResult->addSource(
            'test1',
            'http://example.com/',
            Resource::METHOD_GET,
            array('accept' => '*/*')
        );
        $this->theResult->addSource(
            'test2',
            'http://example.org/',
            Resource::METHOD_POST,
            array('content-type' => 'application/json'),
            array('foo' => 'bar')
        );
        $sources = $this->theResult->getSources();

        $this->assertContains(array(
            'type' => 'test1',
            'url' => 'http://example.com/',
            'method' => Resource::METHOD_GET,
            'headers' => array('accept' => '*/*'),
            'payload' => array(),
        ), $sources);
        $this->assertContains(array(
            'type' => 'test2',
            'url' => 'http://example.org/',
            'method' => Resource::METHOD_POST,
            'headers' => array('content-type' => 'application/json'),
            'payload' => array('foo' => 'bar'),
        ), $sources);
        $this->assertTrue($this->theResult->getStatus());
    }
}
