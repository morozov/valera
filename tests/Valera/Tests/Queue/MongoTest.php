<?php

namespace Valera\Tests\Queue;

use Valera\Queue\Mongo as Queue;
use Valera\Serialize\ArraySerializer;
use Valera\Tests\Helper;

/**
 * @requires extension mongo
 */
class MongoTest extends AbstractTest
{
    public static function setUpBeforeClass()
    {
        $db = Helper::getMongo();
        self::$queue = new Queue($db, 'test');

        parent::setUpBeforeClass();
    }
}
