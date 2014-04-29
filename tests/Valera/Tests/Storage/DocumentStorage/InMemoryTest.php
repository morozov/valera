<?php

namespace Valera\Tests\Storage\DocumentStorage;

use Valera\Storage\DocumentStorage\InMemory as Storage;

class InMemoryTest extends AbstractTest
{
    public static function setUpBeforeClass()
    {
        self::$storage = new Storage();
        parent::setUpBeforeClass();
    }
}
