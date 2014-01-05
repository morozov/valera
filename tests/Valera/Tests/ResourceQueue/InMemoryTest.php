<?php

namespace Valera\Tests\ResourceQueue;

use Valera\ResourceQueue\InMemory as Queue;

class InMemoryTest extends AbstractTest
{
    public static function setUpBeforeClass()
    {
        self::$queue = new Queue();
        parent::setUpBeforeClass();
    }
}
