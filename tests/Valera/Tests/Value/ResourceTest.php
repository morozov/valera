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
        $this->assertNull($resource->getPayload());
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
        $this->assertEquals('http://example.com/path', $resource->getUrl());
    }

    public function testAbsoluteUrlWithReferrer()
    {
        $resource = new Resource('http://example.org/', 'http://example.com/');
        $this->assertEquals('http://example.org/', $resource->getUrl());
    }

    /** @test */
    public function referrerDoesNotAffectHash()
    {
        $r1 = new Resource('http://example.org/');
        $r2 = new Resource('http://example.org/', 'http://example.com/');

        $this->assertEquals($r1->getHash(), $r2->getHash());
    }
}
