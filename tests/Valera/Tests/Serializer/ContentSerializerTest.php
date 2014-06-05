<?php

namespace Valera\Tests\Serializer;

use Valera\Tests\Value\Helper as ValueHelper;

/**
 * @covers Valera\Serializer\ContentSerializer
 * @uses Valera\Content
 * @uses Valera\Resource
 * @uses Valera\Source
 * @uses Valera\Source\DocumentSource
 * @uses Valera\Serializer\ResourceSerializer
 * @uses Valera\Serializer\SourceSerializer
 */
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
