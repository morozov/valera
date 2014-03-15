<?php

namespace Valera\Tests\ResourceQueue;

use Valera\ResourceQueue\Mongo as Queue;

/**
 * @requires extension mongo
 */
class MongoTest extends AbstractTest
{
    public static function setUpBeforeClass()
    {
        if (!defined('VALERA_TESTS_MONGO_DB')) {
            throw new \PHPUnit_Framework_SkippedTestSuiteError(
                'MongoDB database configuration not found'
            );
        }

        if (defined('VALERA_TESTS_MONGO_SERVER')) {
            $client = new \MongoClient(VALERA_TESTS_MONGO_SERVER);
        } else {
            $client = new \MongoClient();
        }

        $db = $client->selectDB(VALERA_TESTS_MONGO_DB);
        self::$queue = new Queue($db);

        parent::setUpBeforeClass();
    }
}
