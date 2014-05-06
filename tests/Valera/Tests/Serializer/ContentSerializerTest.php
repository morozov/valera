<?php

namespace Valera\Tests\Serializer;

class ContentSerializerTest extends AbstractTest
{
    public static function setUpBeforeClass()
    {
        self::$serializer = Helper::getContentSerializer();
    }

    public static function provider()
    {
        return array(
            array(
                Helper::getContent(),
                Helper::getSerializedContent(),
            ),
        );
    }
}
