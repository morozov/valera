<?php

namespace Valera\Tests\Storage\BlobStorage;

use Valera\Storage\BlobStorage\InMemory as Storage;

class InMemoryTest extends AbstractTest
{
    public static function setUpBeforeClass()
    {
        self::$storage = new Storage();
        parent::setUpBeforeClass();
    }
}
