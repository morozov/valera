<?php

namespace Valera\Tests\Serializer;

class SourceSerializerTest extends AbstractTest
{
    public static function setUpBeforeClass()
    {
        self::$serializer = Helper::getSourceSerializer();
    }

    public static function provider()
    {
        return array(
            array(
                Helper::getSource(),
                Helper::getSerializedSource(),
            ),
        );
    }
}
