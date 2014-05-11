<?php

namespace Valera\Tests\Storage\DocumentStorage;

use Valera\Tests\Serializer\Helper;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Valera\Storage\DocumentStorage
     */
    protected static $storage;

    /**
     * @var array
     */
    protected static $data;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$storage->clean();

        self::$data = array('name' => 'value');
    }

    protected function tearDown()
    {
        self::$storage->clean();
    }

    /**
     * @test
     */
    public function defaults()
    {
        $this->assertCount(0, self::$storage);
    }

    /**
     * @test
     * @depends defaults
     */
    public function create()
    {
        // document is stored
        self::$storage->create('foo', self::$data, array());
        $this->assertCount(1, self::$storage);

        // another document is stored
        self::$storage->create('bar', self::$data, array());
        $this->assertCount(2, self::$storage);
    }

    /**
     * @test
     * @depends create
     * @expectedException \DomainException
     */
    public function createDuplicate()
    {
        self::$storage->create('foo', self::$data, array());
        self::$storage->create('foo', self::$data, array());
    }

    /**
     * @test
     * @depends create
     */
    public function retrieve()
    {
        self::$storage->create('foo', self::$data, array());

        // existing document is retrieved
        $foo = self::$storage->retrieve('foo');
        $this->assertEquals(self::$data, $foo);

        // retrieval of non-existing document returns NULL
        $bar = self::$storage->retrieve('bar');
        $this->assertNull($bar);
    }

    /**
     * @test
     * @depends create
     * @depends retrieve
     */
    public function update()
    {
        self::$storage->create('foo', self::$data, array());

        // existing document is updated
        self::$storage->update('foo', array('other' => 'updated'), array());
        $foo = self::$storage->retrieve('foo');
        $this->assertEquals(array(
            'other' => 'updated',
        ), $foo);

        // non-existing document is not created
        self::$storage->update('bar', array('other' => 'updated'), array());
        $bar = self::$storage->retrieve('bar');
        $this->assertNull($bar);
    }

    /**
     * @test
     * @depends create
     * @depends retrieve
     */
    public function delete()
    {
        self::$storage->create('foo', self::$data, array());

        // document is deleted
        self::$storage->delete('foo');
        $foo = self::$storage->retrieve('foo');
        $this->assertNull($foo);
    }

    /**
     * @test
     * @depends create
     * @depends retrieve
     * @depends update
     * @depends delete
     */
    public function findByResource()
    {
        $r1 = Helper::getResource();
        $r2 = Helper::getAnotherResource();
        self::$storage->create('foo', self::$data, array($r1));

        // document is found by related resources
        $documents = self::$storage->findByResource($r1);
        $documents = iterator_to_array($documents);
        $this->assertCount(1, $documents);
        $this->assertArrayHasKey('foo', $documents);
        $this->assertEquals(self::$data, $documents['foo']);

        // document is not found by unrelated resources
        $documents = self::$storage->findByResource($r2);
        $this->assertCount(0, $documents);

        // update document with new related resources
        self::$storage->update('foo', self::$data, array($r2));

        // document is found by related resources
        $documents = self::$storage->findByResource($r2);
        $documents = iterator_to_array($documents);
        $this->assertCount(1, $documents);
        $this->assertArrayHasKey('foo', $documents);
        $this->assertEquals(self::$data, $documents['foo']);

        // document is not found by unrelated resources
        $documents = self::$storage->findByResource($r1);
        $this->assertCount(0, $documents);

        // delete document
        self::$storage->delete('foo');

        // document is not found
        $documents = self::$storage->findByResource($r2);
        $this->assertCount(0, $documents);
    }

    /**
     * @test
     * @depends create
     */
    public function iterator()
    {
        $document = Helper::getDocument();
        self::$storage->create('foo', $document, array());
        $array = iterator_to_array(self::$storage);
        $this->assertEquals(array(
            'foo' => $document,
        ), $array);
    }
}
