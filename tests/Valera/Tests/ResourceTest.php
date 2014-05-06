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
        $this->assertNull($resource->getData());
    }

    public function testEquals()
    {
        $resource1 = new Resource('http://google.com');
        $resource2 = new Resource('http://google.com');

        $this->assertTrue($resource1->equals($resource2));
        
        $resource3 = new Resource('http://ya.ru');
        $this->assertFalse($resource1->equals($resource3));
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
        new Resource(42);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadMethod()
    {
        new Resource('http://url', null, 42);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidationFailed()
    {
        new Resource('not an url');
    }
}
