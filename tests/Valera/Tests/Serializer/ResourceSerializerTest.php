<?php

namespace Valera\Tests\Serializer;

use Valera\Resource;

class ResourceSerializerTest extends AbstractTest
{
    public static function setUpBeforeClass()
    {
        self::$serializer = Helper::getResourceSerializer();
    }

    public static function provider()
    {
        return array(
            array(
                Helper::getResource(),
                Helper::getSerializedResource(),
            ),
            array(
                new Resource('http://example/com/'),
                array(
                    'url' => 'http://example/com/',
                ),
            ),
        );
    }
}
