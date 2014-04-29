<?php

namespace Valera\Tests\Storage\DocumentStorage;

use Valera\Storage\DocumentStorage\Mongo as Storage;
use Valera\Tests\Helper;

class MongoTest extends AbstractTest
{
    public static function setUpBeforeClass()
    {
        $db = Helper::getMongo();
        self::$storage = new Storage($db);

        parent::setUpBeforeClass();
    }
}
