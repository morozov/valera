<?php

$autoload = __DIR__ . '/../vendor/autoload.php';

if (!file_exists($autoload)) {
    echo 'You must set up the project dependencies, run the following commands:'
        . PHP_EOL . 'curl -sS https://getcomposer.org/installer | php'
        . PHP_EOL . 'php composer.phar install'
        . PHP_EOL;
    exit(1);
}

require $autoload;

class Factory implements Valera\Worker\ParserFactoryInterface
{

    public function getParser($type)
    {
        if ($type == 'page') {
            return function ($content) {
                $parsed = strip_tags($content);
                return $parsed;
            };
        } else {
            return function ($content) {
                return false;
            };
        }

    }
}

$successCallback = function ($result) {
    echo "RESULT: " . $result, PHP_EOL;
};

$failCallback = function () {
    echo "Something goes wrong", PHP_EOL;
};

$testContentString = "<html><body><h1>Hello Valera</h1></body></html>";
$testContentString2 = "<html><body><h2>Today is Saturday</h2></body></html>";

$fakeResource = new Valera\Resource('page', 'http://google.com');
$fakeResource2 = new Valera\Resource('unknown', 'http://example.com');

$content = new Valera\Content($testContentString, $fakeResource);
$content2 = new Valera\Content($testContentString2, $fakeResource2);

$parser = new Valera\Worker\Parser(new Factory());

$parser->setSuccessCallback($successCallback)
    ->setFailureCallback($failCallback)
    ->addJob($content)
    ->addJob($content2)
    ->run();
