<?php

namespace Valera\Tests\Serializer;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Valera\Serializer\SerializerInterface
     */
    protected static $serializer;

    /**
     * @test
     * @dataProvider provider
     */
    public function serialize($unserialized, $serialized)
    {
        $actual = self::$serializer->serialize($unserialized);
        $this->assertEquals($serialized, $actual);
    }

    /**
     * @test
     * @dataProvider provider
     */
    public function unserialize($unserialized, $serialized)
    {
        $actual = self::$serializer->unserialize($serialized);
        $this->assertEquals($unserialized, $actual);
    }
}
