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

$client = new \Valera\Worker\CurlClient();

$google = new \Valera\Resource('page', 'http://google.com');
$yandex = new \Valera\Resource('page', 'http://yandex.by');
$bing = new \Valera\Resource('page', 'http://bing.com');

$callback = function (\Valera\Response $response) {
    $content = new \Valera\Content(strval($response->getContent()), $response->getResource());
    $text = $content->getContent();
    printf("Fetched %s, strlen=%s\n", $content->getResource()->getUrl(), mb_strlen($text));
};

$client->setSuccessCallback($callback);

$client->addJob($google)
       ->addJob($yandex)
       ->addJob($bing)
       ->run();
