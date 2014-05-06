<?php

namespace Valera\Tests\Serializer;

class DocumentSerializerTest extends AbstractTest
{
    public static function setUpBeforeClass()
    {
        self::$serializer = Helper::getDocumentSerializer();
    }

    public static function provider()
    {
        return array(
            array(
                Helper::getDocument(),
                Helper::getSerializedDocument(),
            ),
        );
    }
}
