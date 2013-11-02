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

$client = new \Valera\Fetch\CurlClient();

$google = new \Valera\Resource('http://google.com');
$yandex = new \Valera\Resource('http://yandex.by');
$bing = new \Valera\Resource('http://bing.com');

$callback = function(\Valera\ContentInterface $content) {
    $text = $content->getContent();
    printf("Fetched %s, strlen=%s\n", $content->getResource()->getUrl(), mb_strlen($content->getContent()));
};

$client->setSuccessCallback($callback);

$client->addResource($google)
       ->addResource($yandex)
       ->addResource($bing)
       ->fetch();
