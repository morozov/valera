<?php

namespace Valera\Tests\Value;

/**
 * @covers \Valera\Value\Reference
 * @uses \Valera\Resource
 * @uses \Valera\Value\ResourceData
 */
class ReferenceTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function getResource()
    {
        $reference = Helper::getReference();
        $referrer = Helper::getReferrer();

        $resource = $reference->getResource($referrer);
        $this->assertEquals('http://example.com/path', $resource->getUrl());
        $this->assertEquals('http://example.com/', $resource->getReferrer());
    }
}
