<?php

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->add('Valera\Tests', __DIR__);

if (is_readable(__DIR__.'/config.php')) {
    require_once __DIR__.'/config.php';
}
