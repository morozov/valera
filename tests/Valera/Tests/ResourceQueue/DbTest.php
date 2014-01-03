<?php

namespace Valera\Tests\ResourceQueue;

use Valera\ResourceQueue\Db;

/**
 * @requires extension pdo_sqlite
 */
class DbTest extends AbstractTest
{
    //private $db;

    protected function setUp()
    {
        parent::setUp();

        //$this->db = tempnam(sys_get_temp_dir(), 'phpunit');

        $conn = new \PDO('mysql:dbname=valera', 'root', '');
        $this->queue = new Db($conn);
        $this->queue->clean();
    }

    protected function tearDown()
    {
        //unlink($this->db);
        parent::tearDown();
    }
}
