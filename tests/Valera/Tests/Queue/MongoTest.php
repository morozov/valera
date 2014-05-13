<?php

namespace Valera\Tests\Queue;

use Valera\Queue\Mongo as Queue;
use Valera\Tests\Helper;
use Valera\Tests\Serializer\Helper as SerializerHelper;

/**
 * @requires extension mongo
 */
class MongoTest extends AbstractTest
{
    public static function setUpBeforeClass()
    {
        $db = Helper::getMongo();
        $sourceSerializer = SerializerHelper::getSourceSerializer();

        self::$queue = new Queue('test', $db, $sourceSerializer);

        parent::setUpBeforeClass();
    }
}
