<?php

namespace Valera\Tests\ResourceQueue;

use Valera\ResourceQueue\InMemory;

class InMemoryTest extends AbstractTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->queue = new InMemory();
    }
}
