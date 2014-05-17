<?php

namespace Valera\Tests\Value;

use Valera\Source;

class SourceTest extends \PHPUnit_Framework_TestCase
{
    public function testApi()
    {
        $resource = Helper::getResource();
        $source = new Source('source-test', $resource);

        $this->assertEquals('source-test', $source->getType());
        $this->assertEquals($resource, $source->getResource());
        $this->assertEquals($resource->getHash(), $source->getHash());
    }
}
