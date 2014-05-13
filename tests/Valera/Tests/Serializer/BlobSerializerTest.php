<?php

namespace Valera\Tests\Serializer;

use Valera\Tests\Value\Helper as ValueHelper;

class BlobSerializerTest extends AbstractTest
{
    public static function setUpBeforeClass()
    {
        self::$serializer = Helper::getBlobSerializer();
    }

    public static function provider()
    {
        return array(
            array(
                ValueHelper::getBlob(),
                Helper::getSerializedBlob(),
            ),
            array(
                ValueHelper::getAnotherBlob(),
                Helper::getAnotherSerializedBlob(),
            ),
        );
    }
}
