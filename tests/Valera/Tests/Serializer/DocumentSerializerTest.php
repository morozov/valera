<?php

namespace Valera\Tests\Serializer;

use Valera\Entity\Document;
use Valera\Tests\Value\Helper as ValueHelper;

/**
 * @covers Valera\Serializer\DocumentSerializer
 * @uses Valera\Blob
 * @uses Valera\Entity\Document
 * @uses Valera\Resource
 * @uses Valera\Value\ResourceData
 * @uses Valera\Serializer\BlobSerializer
 * @uses Valera\Serializer\ResourceSerializer
 */
class DocumentSerializerTest extends AbstractTest
{
    public static function setUpBeforeClass()
    {
        self::$serializer = Helper::getDocumentSerializer();
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     */
    public function serializeUnexpected()
    {
        $document = new Document('test-unexpected', array(
            'foo' => new \stdClass(),
        ));

        self::$serializer->serialize($document);
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     */
    public function unserializeUnexpected()
    {
        self::$serializer->unserialize(array(
            'id' => 'test-unexpected',
            'data' => array(
                'foo' => array(
                    '_type' => 'unknown',
                ),
            ),
        ));
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
