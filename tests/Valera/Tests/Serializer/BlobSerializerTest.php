<?php

namespace Valera\Tests\Serializer;

use Valera\Tests\Value\Helper as ValueHelper;

/**
 * @covers Valera\Serializer\BlobSerializer
 * @uses Valera\Blob
 * @uses Valera\Resource
 * @uses Valera\Value\ResourceData
 * @uses Valera\Serializer\ResourceSerializer
 */
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
