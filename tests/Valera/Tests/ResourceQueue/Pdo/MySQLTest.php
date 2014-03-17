<?php

namespace Valera\Tests\Queue\Pdo;

/**
 * @requires extension pdo_mysql
 */
class MySQLTest extends AbstractTest
{
    public static function setUpBeforeClass()
    {
        if (!defined('VALERA_TESTS_MYSQL_DSN')
            || !defined('VALERA_TESTS_MYSQL_USERNAME')
            || !defined('VALERA_TESTS_MYSQL_PASSWORD')
        ) {
            throw new \PHPUnit_Framework_SkippedTestSuiteError(
                'MySQL database configuration not found'
            );
        }

        self::$conn = new \PDO(
            VALERA_TESTS_MYSQL_DSN,
            VALERA_TESTS_MYSQL_USERNAME,
            VALERA_TESTS_MYSQL_PASSWORD
        );
        parent::setUpBeforeClass();
    }
}
