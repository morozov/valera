<?php

namespace Valera\Tests\ResourceQueue;

use Valera\ResourceQueue\Db;

/**
 * @requires extension pdo_sqlite
 */
class DbTest extends AbstractTest
{
    private $db;

    protected function setUp()
    {
        parent::setUp();

        $this->db = tempnam(sys_get_temp_dir(), 'phpunit');
        $this->queue = new Db();
        $this->queue->setUp();
    }

    protected function tearDown()
    {
        unlink($this->db);
        parent::tearDown();
    }
}
