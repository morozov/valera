<?php

namespace Valera\Tests\Serializer;

use Valera\Tests\Value\Helper as ValueHelper;

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
                ValueHelper::getDocument(),
                Helper::getSerializedDocument(),
            ),
        );
    }
}
