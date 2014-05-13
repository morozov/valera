<?php

namespace Valera\Tests\Value;

use Valera\Resource;

class ResourceTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $resource = new Resource('http://google.com');
        $this->assertEquals('http://google.com', $resource->getUrl());
        $this->assertEquals(Resource::METHOD_GET, $resource->getMethod());
        $this->assertEquals(array(), $resource->getHeaders());
        $this->assertNull($resource->getData());
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
