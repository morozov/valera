<?php

namespace Valera\Tests\Queue\Pdo;

/**
 * @requires extension pdo_sqlite
 */
class SQLiteTest extends AbstractTest
{
    public static function setUpBeforeClass()
    {
        if (!defined('VALERA_TESTS_SQLITE_DSN')) {
            throw new \PHPUnit_Framework_SkippedTestSuiteError(
                'SQLite database configuration not found'
            );
        }

        self::$conn = new \PDO(VALERA_TESTS_SQLITE_DSN);
        self::$conn->exec('PRAGMA foreign_keys = ON');
        parent::setUpBeforeClass();
    }
}
