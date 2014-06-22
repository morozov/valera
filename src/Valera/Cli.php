<?php

namespace Valera;

use Ulrichsg\Getopt\Getopt;

class Cli
{
    /**
     * @var Api
     */
    protected $api;

    /**
     * @var Getopt
     */
    protected $getOpt;

    public function __construct(Api $api, Getopt $getOpt)
    {
        $this->api = $api;

        $getOpt->addOptions(array(
            array(null, 'restart', Getopt::OPTIONAL_ARGUMENT),
            array('f', 'force', Getopt::NO_ARGUMENT),
        ));
        $this->getOpt = $getOpt;
    }

    public function run(array $arguments)
    {
        $this->getOpt->parse($arguments);
        $restart = $this->getOpt->getOption('restart');
        $force = (boolean) $this->getOpt->getOption('force');

        if ($restart === 1) {
            $restart = 'loader';
            $force = true;
        }

        switch ($restart) {
            case 'parser':
                $this->api->restartParser($force);
                break;
            case 'loader':
                $this->api->restartLoader($force);
                break;
        }

        $this->api->run();
    }
}
