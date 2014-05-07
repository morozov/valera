<?php

namespace Valera\Tests\Storage\DocumentStorage;

use Valera\DocumentIterator;
use Valera\Serializer\BlobSerializer;
use Valera\Serializer\DocumentSerializer;
use Valera\Serializer\ResourceSerializer;
use Valera\Storage\DocumentStorage\Mongo as Storage;
use Valera\Tests\Helper;

class MongoTest extends AbstractTest
{
    public static function setUpBeforeClass()
    {
        $db = Helper::getMongo();
        $resourceSerializer = new ResourceSerializer();
        $documentSerializer = new DocumentSerializer(
            new DocumentIterator(),
            $resourceSerializer,
            new BlobSerializer($resourceSerializer)
        );
        self::$storage = new Storage($db, $documentSerializer);

        parent::setUpBeforeClass();
    }
}
