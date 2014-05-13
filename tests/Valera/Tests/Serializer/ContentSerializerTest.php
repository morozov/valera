<?php

namespace Valera\Tests\Serializer;

use Valera\Tests\Value\Helper as ValueHelper;

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
                ValueHelper::getContent(),
                Helper::getSerializedContent(),
            ),
            array(
                ValueHelper::getAnotherContent(),
                Helper::getAnotherSerializedContent(),
            ),
        );
    }
}
