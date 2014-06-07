<?php

namespace Valera\Tests\Value;

use Valera\Resource;

class ResourceTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $resource = new Resource('http://google.com');
        $this->assertEquals('http://google.com', $resource->getUrl());
        $this->assertEquals(null, $resource->getReferrer());
        $this->assertEquals(Resource::METHOD_GET, $resource->getMethod());
        $this->assertEquals(array(), $resource->getHeaders());
        $this->assertNull($resource->getData());
        $this->assertInternalType('string', $resource->getHash());
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

    public function testRelativeUrl()
    {
        $resource = new Resource('path', 'http://example.com/');

        $this->assertEquals('path', $resource->getUrl());
        $this->assertEquals('http://example.com/', $resource->getReferrer());
    }
}
