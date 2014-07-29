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
            array(null, 'max-items', Getopt::OPTIONAL_ARGUMENT),
            array(null, 'max-items-code', Getopt::OPTIONAL_ARGUMENT),
        ));
        $this->getOpt = $getOpt;
    }

    public function run(array $arguments)
    {
        $this->getOpt->parse($arguments);
        $restart = $this->getOpt->getOption('restart');
        $force = (boolean) $this->getOpt->getOption('force');
        $maxItems = $this->getOpt->getOption('max-items');
        if ($maxItems !== null) {
            // validate $maxItems
            $maxItemsCode = $this->getOpt->getOption('max-items-code');
            // validate $maxItemsCode
        } else {
            $maxItemsCode = 0;
        }

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

        $total = $this->api->run($maxItems);
        if ($total >= $maxItems) {
            return 0;
        } else {
            return $maxItemsCode;
        }
    }
}
