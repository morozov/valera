<?php

namespace Valera\Tests\Storage\DocumentStorage;

use Valera\Tests\Value\Helper;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Valera\Storage\DocumentStorage
     */
    protected static $storage;

    /**
     * @var \Valera\Entity\Document
     */
    protected $document;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$storage->clean();
    }

    public function setUp()
    {
        $this->document = Helper::getDocument();
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
        self::$storage->create($this->document);
        $this->assertCount(1, self::$storage);

        // another document is stored
        $d2 = Helper::getAnotherDocument();
        self::$storage->create($d2);
        $this->assertCount(2, self::$storage);
    }

    /**
     * @test
     * @depends create
     * @expectedException \DomainException
     */
    public function createDuplicate()
    {
        self::$storage->create($this->document);
        self::$storage->create($this->document);
    }

    /**
     * @test
     * @depends create
     */
    public function retrieve()
    {
        self::$storage->create($this->document);

        // existing document is retrieved
        $d1 = self::$storage->retrieve($this->document->getId());
        $this->assertEquals($this->document, $d1);

        // retrieval of non-existing document returns NULL
        $unknown = self::$storage->retrieve('unknown');
        $this->assertNull($unknown);
    }

    /**
     * @test
     * @depends create
     * @depends retrieve
     */
    public function update()
    {
        self::$storage->create($this->document);

        $this->document->update(function () {
            return array(
                'other' => 'updated',
            );
        });

        // existing document is updated
        self::$storage->update($this->document);
        $document = self::$storage->retrieve($this->document->getId());
        $this->assertEquals(array(
            'other' => 'updated',
        ), $document->getData());

        // non-existing document is not created
        $anotherDocument = Helper::getAnotherDocument();
        self::$storage->update($anotherDocument);
        $document = self::$storage->retrieve($anotherDocument->getId());
        $this->assertNull($document);
    }

    /**
     * @test
     * @depends create
     * @depends retrieve
     */
    public function delete()
    {
        self::$storage->create($this->document);

        // document is deleted
        self::$storage->delete($this->document->getId());
        $d1 = self::$storage->retrieve($this->document->getId());
        $this->assertNull($d1);
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
        self::$storage->create($this->document);

        // document is found by related resources
        $documents = self::$storage->findByResource($r1);
        $documents = iterator_to_array($documents);
        $this->assertCount(1, $documents);

        $id = $this->document->getId();
        $this->assertArrayHasKey($id, $documents);
        $this->assertEquals($this->document, $documents[$id]);

        // document is not found by unrelated resources
        $documents = self::$storage->findByResource($r2);
        $this->assertCount(0, $documents);

        // update document with new related resources
        $this->document->update(function () use ($r2) {
            return array($r2);
        });
        self::$storage->update($this->document);

        // document is found by related resources
        $documents = self::$storage->findByResource($r2);
        $documents = iterator_to_array($documents);
        $this->assertCount(1, $documents);

        // document is not found by unrelated resources
        $documents = self::$storage->findByResource($r1);
        $this->assertCount(0, $documents);

        // delete document
        self::$storage->delete($id);

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
        self::$storage->create($this->document);
        $array = iterator_to_array(self::$storage);
        $this->assertEquals(array(
            $this->document->getId() => $this->document,
        ), $array);
    }
}
