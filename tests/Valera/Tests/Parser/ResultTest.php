<?php

namespace Valera\Tests\Parser;

use Valera\Parser\Result;
use Valera\Resource;

/**
 * @covers \Valera\Parser\Result
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Valera\Parser\Result
     */
    private $result;
    
    protected function setUp()
    {
        $this->result = new Result();
    }

    /** @test */
    public function defaults()
    {
        $this->result->resolve();
        $this->assertEmpty($this->result->getNewDocuments());
        $this->assertEmpty($this->result->getUpdatedDocuments());
        $this->assertEmpty($this->result->getBlobs());
        $this->assertEmpty($this->result->getSources());
    }

    /**
     * @test
     * @depends defaults
     */
    public function addDocument()
    {
        $this->result->addDocument('test1', array('foo1' => 'bar1'));
        $this->result->addDocument('test2', array('foo2' => 'bar2'));
        $newDocuments = $this->result->getNewDocuments();

        $this->assertEquals(array(
            'test1' => array('foo1' => 'bar1'),
            'test2' => array('foo2' => 'bar2'),
        ), $newDocuments);
        $this->assertTrue($this->result->getStatus());
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function addDocumentDuplicate()
    {
        $this->result->addDocument('test', array('foo1' => 'bar1'));
        $this->result->addDocument('test', array('foo2' => 'bar2'));
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
        
        $this->result->updateDocument('test1', $callback1);
        $this->result->updateDocument('test2', $callback2);
        $updatedDocuments = $this->result->getUpdatedDocuments();

        $this->assertEquals(array(
            'test1' => $callback1,
            'test2' => $callback1,
        ), $updatedDocuments);
        $this->assertTrue($this->result->getStatus());
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

        $this->result->updateDocument('test', $callback1);
        $this->result->updateDocument('test', $callback2);
    }

    /**
     * @test
     * @depends defaults
     */
    public function addBlob()
    {
        $this->result->addBlob('contents');
        $blobs = $this->result->getBlobs();

        $this->assertEquals(array(
            'contents',
        ), $blobs);
        $this->assertTrue($this->result->getStatus());
    }

    /**
     * @test
     * @depends defaults
     */
    public function addSource()
    {
        $this->result->addSource(
            'test1',
            'http://example.com/',
            Resource::METHOD_GET,
            array('accept' => '*/*')
        );
        $this->result->addSource(
            'test2',
            'http://example.org/',
            Resource::METHOD_POST,
            array('content-type' => 'application/json'),
            array('foo' => 'bar')
        );
        $sources = $this->result->getSources();

        $this->assertContains(array(
            'type' => 'test1',
            'url' => 'http://example.com/',
            'method' => Resource::METHOD_GET,
            'headers' => array('accept' => '*/*'),
            'data' => array(),
        ), $sources);
        $this->assertContains(array(
            'type' => 'test2',
            'url' => 'http://example.org/',
            'method' => Resource::METHOD_POST,
            'headers' => array('content-type' => 'application/json'),
            'data' => array('foo' => 'bar'),
        ), $sources);
        $this->assertTrue($this->result->getStatus());
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function addSourceDuplicate()
    {
        $this->result->addSource('test1', 'http://example.com/');
        $this->result->addSource('test2', 'http://example.com/');
    }
}
