<?php

namespace Valera\Tests\Serializer;

use Valera\Tests\Value\Helper as ValueHelper;

/**
 * @covers Valera\Serializer\SourceSerializer
 * @uses Valera\Resource
 * @uses Valera\Value\ResourceData
 * @uses Valera\Serializer\ResourceSerializer
 * @uses Valera\Source
 * @uses Valera\Source\DocumentSource
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
                ValueHelper::getDocumentSource(),
                Helper::getSerializedDocumentSource(),
            ),
            array(
                ValueHelper::getBlobSource(),
                Helper::getSerializedBlobSource(),
            ),
        );
    }
}
