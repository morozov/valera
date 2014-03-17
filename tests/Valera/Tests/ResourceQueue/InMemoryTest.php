<?php

namespace Valera\Tests\Queue;

use Valera\Queue\InMemory as Queue;

class InMemoryTest extends AbstractTest
{
    public static function setUpBeforeClass()
    {
        self::$queue = new Queue();
        parent::setUpBeforeClass();
    }
}
