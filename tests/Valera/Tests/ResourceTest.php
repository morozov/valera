<?php

namespace Valera\Tests;
use Valera\Resource;

class ResourceTest extends \PHPUnit_Framework_TestCase
{

    public function testDefaults()
    {
        $resource = new Resource('http://google.com');
        $this->assertEquals('http://google.com', $resource->getUrl());
        $this->assertEquals('GET', $resource->getMethod());
        $this->assertEquals(array(), $resource->getHeaders());
        $this->assertEquals(array(), $resource->getData());
    }

    public function testEquals()
    {
        $resource = new Resource('http://google.com');
        $resource2 = new Resource('http://google.com');

        $this->assertTrue($resource->equals($resource2));

        $resource3 = new Resource('http://ya.ru', Resource::METHOD_POST);
        $this->assertFalse($resource->equals($resource3));
    }

    public function testSerializable()
    {
        $resource = new Resource('http://google.com');
        $serialized = serialize($resource);
        $resource2 = unserialize($serialized);
        $this->assertTrue($resource->equals($resource2));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadUrlType()
    {
        $resource = new Resource(42);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadMethod()
    {
        $resource = new Resource('http://url', 42);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidationFailed()
    {
        $resource = new Resource('not an url');
    }
}
