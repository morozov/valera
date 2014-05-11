<?php

namespace Valera\Tests\Storage\BlobStorage;

use Valera\Resource;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Valera\Storage\BlobStorage
     */
    protected static $storage;

    /**
     * @var \Valera\Resource
     */
    protected static $r1;
    protected static $r2;

    /**
     * @var string
     */
    protected static $data = 'some-contents';

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$r1 = new Resource('http://example.com/');
        self::$r2 = new Resource('http://example.org/');
        self::$storage->clean();
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
        // blob is stored
        self::$storage->create(self::$r1, self::$data);
        $this->assertCount(1, self::$storage);

        // another blob is stored
        self::$storage->create(self::$r2, self::$data);
        $this->assertCount(2, self::$storage);
    }

    /**
     * @test
     * @depends create
     * @expectedException \DomainException
     */
    public function createDuplicate()
    {
        self::$storage->create(self::$r1, self::$data);
        self::$storage->create(self::$r1, self::$data);
    }

    /**
     * @test
     * @depends create
     */
    public function retrieve()
    {
        self::$storage->create(self::$r1, self::$data);

        // existing blob is retrieved
        $data1 = self::$storage->retrieve(self::$r1);
        $this->assertEquals(self::$data, $data1);

        // retrieval of non-existing blob returns NULL
        $data2 = self::$storage->retrieve(self::$r2);
        $this->assertNull($data2);
    }

    /**
     * @test
     * @depends create
     */
    public function delete()
    {
        self::$storage->create(self::$r1, self::$data);

        // blob is deleted
        self::$storage->delete(self::$r1);
        $data = self::$storage->retrieve(self::$r1);
        $this->assertNull($data);
    }
}
