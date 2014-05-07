<?php

namespace Valera\Tests\Queue;

use Valera\Queue\Mongo as Queue;
use Valera\Serializer\ResourceSerializer;
use Valera\Serializer\SourceSerializer;
use Valera\Tests\Helper;

/**
 * @requires extension mongo
 */
class MongoTest extends AbstractTest
{
    public static function setUpBeforeClass()
    {
        $db = Helper::getMongo();
        $sourceSerializer = new SourceSerializer(
            new ResourceSerializer()
        );

        self::$queue = new Queue('test', $db, $sourceSerializer);

        parent::setUpBeforeClass();
    }
}
