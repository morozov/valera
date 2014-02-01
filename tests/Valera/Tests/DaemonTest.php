<?php

namespace Valera\Tests;
use Valera\Daemon;

class DaemonTest extends \PHPUnit_Framework_TestCase
{
    public function testSetGet()
    {
        $stdIn  = '/tmp/valera_stdin';
        Daemon::setStdIn($stdIn);
        $this->assertEquals($stdIn, Daemon::getStdIn());

        $stdOut = '/tmp/valera_stdout';
        Daemon::setStdOut($stdOut);
        $this->assertEquals($stdOut, Daemon::getStdOut());

        $stdErr = '/tmp/valera_stderr';
        Daemon::setStdErr($stdErr);
        $this->assertEquals($stdErr, Daemon::getStdErr());

    }

    /**
    public function testRun()
    {
        $loopHandler = function() {
            static $i = 0;
            if (!$i) {
                echo "test string";
                $i++;
                return true;
            }
            return false;
        };

        $stdOut = '/tmp/valera_stdout';
        Daemon::setStdOut($stdOut);

        Daemon::run($loopHandler);

        $this->assertEquals('test string', file_get_contents($stdOut));

    }
    */
}
