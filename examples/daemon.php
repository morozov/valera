<?php
declare(ticks = 1);
$autoload = __DIR__ . '/../vendor/autoload.php';

if (!file_exists($autoload)) {
    echo 'You must set up the project dependencies, run the following commands:'
        . PHP_EOL . 'curl -sS https://getcomposer.org/installer | php'
        . PHP_EOL . 'php composer.phar install'
        . PHP_EOL;
    exit(1);
}

require $autoload;

$loopHandler = function() {
    static $i = 0;
    if ($i<5) {
        echo "STEP $i", PHP_EOL;
        $i++;
        return true;
    }
    return false;
};


\Valera\Daemon::setStdOut('/tmp/valera_output');
\Valera\Daemon::setSignalHandler(function($signal){
    switch ($signal) {
        case SIGINT:
            echo "sigint";
            exit(0);
        case SIGTERM:
            echo "sigterm";
            exit(0);
    }
});
\Valera\Daemon::run($loopHandler);
