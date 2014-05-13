<?php

namespace Valera\Tests\Serializer;

use Valera\Tests\Value\Helper as ValueHelper;

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
                ValueHelper::getResource(),
                Helper::getSerializedResource(),
            ),
            array(
                ValueHelper::getAnotherResource(),
                Helper::getAnotherSerializedResource(),
            ),
        );
    }
}
