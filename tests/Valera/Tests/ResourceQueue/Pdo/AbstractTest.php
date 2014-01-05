<?php

namespace Valera\Tests\ResourceQueue\Pdo;

use Valera\ResourceQueue\Pdo as Queue;
use Valera\Tests\ResourceQueue\AbstractTest as Base;

/**
 * @requires extension pdo
 */
abstract class AbstractTest extends Base
{
    protected static $conn;

    public static function setUpBeforeClass()
    {
        self::$queue = new Queue(self::$conn);
        parent::setUpBeforeClass();
    }
}
