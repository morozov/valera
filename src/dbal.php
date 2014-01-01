<?php

require __DIR__ . '/../vendor/autoload.php';

$connectionParams = array(
    'dbname' => 'sugarcrm',
    'user' => 'root',
    'password' => '',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
    //'driver' => 'pdo_sqlite',
    //'memory' => true,
);

$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams);

$schema = new \Doctrine\DBAL\Schema\Schema();
$resource = $schema->createTable('resource');
$resource->addColumn('id', 'integer', array(
    'unsigned' => true,
    'autoincrement' => true,
));
$resource->addColumn('hash', 'string', array(
    'length' => 32,
));
$resource->addColumn('data', 'text');
$resource->setPrimaryKey(array('id'));
$resource->addUniqueIndex(array('hash'));

$resourceQueue = $schema->createTable('resource_queue');
$resourceQueue->addColumn('resource_id', 'integer', array(
    'unsigned' => true,
));
$resourceQueue->addColumn('order', 'integer', array(
    'unsigned' => true,
));
$resourceQueue->addUniqueIndex(array('resource_id'));
$resourceQueue->addUniqueIndex(array('order'));
$resourceQueue->addForeignKeyConstraint(
    $resource,
    array('resource_id'),
    array('id')
);

$queries = $schema->toSql($conn->getDatabasePlatform());
var_dump($queries);
