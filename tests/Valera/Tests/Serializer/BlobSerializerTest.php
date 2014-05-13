<?php

namespace Valera\Tests\Serializer;

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
                Helper::getBlob(),
                Helper::getSerializedBlob(),
            ),
            array(
                Helper::getAnotherBlob(),
                Helper::getAnotherSerializedBlob(),
            ),
        );
    }
}
