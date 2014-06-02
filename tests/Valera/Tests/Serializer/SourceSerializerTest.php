<?php

namespace Valera\Tests\Serializer;

use Valera\Tests\Value\Helper as ValueHelper;

/**
 * @covers Valera\Serializer\SourceSerializer
 * @uses Valera\Resource
 * @uses Valera\Serializer\ResourceSerializer
 * @uses Valera\Source
 */
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
                ValueHelper::getSource(),
                Helper::getSerializedSource(),
            ),
        );
    }
}
