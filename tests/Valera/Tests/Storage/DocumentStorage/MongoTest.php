<?php

namespace Valera\Tests\Storage\DocumentStorage;

use Valera\Storage\DocumentStorage\Mongo as Storage;
use Valera\Tests\Helper;
use Valera\Tests\Serializer\Helper as SerializerHelper;

class MongoTest extends AbstractTest
{
    public static function setUpBeforeClass()
    {
        $db = Helper::getMongo();
        $documentSerializer = SerializerHelper::getDocumentSerializer();
        self::$storage = new Storage($db, $documentSerializer);

        parent::setUpBeforeClass();
    }
}
