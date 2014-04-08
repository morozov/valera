<?php

namespace Valera\Tests\Queue\Pdo;

use Valera\Queue\Pdo as Queue;
use Valera\Tests\Queue\AbstractTest as Base;

/**
 * @requires extension pdo
 */
abstract class AbstractTest extends Base
{
    protected static $conn;

    public static function setUpBeforeClass()
    {
        self::$queue = new Queue(self::$conn, 'resource');
        parent::setUpBeforeClass();
    }
}
