<?php

namespace Valera\Tests\Value;

use Valera\Blob;

class BlobTest extends \PHPUnit_Framework_TestCase
{
    public function testApi()
    {
        $resource = Helper::getResource();
        $blob = new Blob('/path/to/blob', $resource);

        $this->assertEquals('/path/to/blob', $blob->getPath());
        $this->assertEquals($resource, $blob->getResource());
    }
}
