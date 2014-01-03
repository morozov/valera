<?php

require __DIR__ . '/../vendor/autoload.php';

$connectionParams = array(
    'dbname' => 'valera',
    'user' => 'root',
    'password' => '',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
    /*'driver' => 'pdo_sqlite',
    'memory' => true,*/
);

$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams);

$schema = new \Doctrine\DBAL\Schema\Schema();

// `resource` table
$resource = $schema->createTable('resource');
$resource->addColumn('hash', 'string', array(
    'length' => 32,
));
$resource->addColumn('data', 'text');
$resource->setPrimaryKey(array('hash'));

// `resource_queue` table
$resourceQueue = $schema->createTable('resource_queue');
$resourceQueue->addColumn('resource_hash', 'string', array(
    'length' => 32,
));
$resourceQueue->addColumn('item_order', 'integer', array(
    'unsigned' => true,
    'autoincrement' => true,
));
$resourceQueue->setPrimaryKey(array('resource_hash'));
$resourceQueue->addUniqueIndex(array('item_order'));
$resourceQueue->addForeignKeyConstraint(
    $resource,
    array('resource_hash'),
    array('hash')
);

// `resource_in_progress` table
$resourceInProgress = $schema->createTable('resource_in_progress');
$resourceInProgress->addColumn('resource_hash', 'string', array(
    'length' => 32,
));
$resourceInProgress->addColumn('start_date', 'datetime');
$resourceInProgress->setPrimaryKey(array('resource_hash'));
$resourceInProgress->addForeignKeyConstraint(
    $resource,
    array('resource_hash'),
    array('hash')
);

// `resource_failed` table
$resourceFailed = $schema->createTable('resource_failed');
$resourceFailed->addColumn('resource_hash', 'string', array(
    'length' => 32,
));
$resourceInProgress->setPrimaryKey(array('resource_hash'));
$resourceInProgress->addForeignKeyConstraint(
    $resource,
    array('resource_hash'),
    array('hash')
);

$queries = $schema->toSql($conn->getDatabasePlatform());
echo implode(';' . PHP_EOL, $queries);
