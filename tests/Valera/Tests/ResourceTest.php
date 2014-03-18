<?php

namespace Valera\Tests;

use Valera\Resource;

class ResourceTest extends \PHPUnit_Framework_TestCase
{

    public function testDefaults()
    {
        $resource = new Resource('page','http://google.com');
        $this->assertEquals('http://google.com', $resource->getUrl());
        $this->assertEquals('GET', $resource->getMethod());
        $this->assertEquals(array(), $resource->getHeaders());
        $this->assertEquals(array(), $resource->getData());
    }

    public function testEquals()
    {
        $resource1 = new Resource('page', 'http://google.com');
        $resource2 = new Resource('page', 'http://google.com');

        $this->assertTrue($resource1->equals($resource2));
        
        $resource3 = new Resource('page', 'http://ya.ru', Resource::METHOD_POST);
        $this->assertFalse($resource1->equals($resource3));
    }

    public function testSerializable()
    {
        $resource = new Resource('page', 'http://google.com');
        $serialized = serialize($resource);
        $resource2 = unserialize($serialized);
        $this->assertTrue($resource->equals($resource2));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadUrlType()
    {
        new Resource('page', 42);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadMethod()
    {
        new Resource('page', 'http://url', 42);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidationFailed()
    {
        new Resource('page', 'not an url');
    }
}
