<?php

namespace Valera\Tests\Worker;

use Valera\Worker\Sequence;

/**
 * @cover Valera\Worker\Sequence
 */
class SequenceTest extends \PHPUnit_Framework_TestCase
{
    public function testSequence()
    {
        $w1 = $this->getWorkerMock(array(1, 1, 0, 0));
        $w2 = $this->getWorkerMock(array(2, 0, 5, 0));
        $w3 = $this->getWorkerMock(array(0, 3, 0));

        $logger = $this->getMock('Psr\\Log\\LoggerInterface');

        $sequence = new Sequence(array($w1, $w2, $w3), $logger);
        $count = $sequence->run();

        $this->assertEquals(12, $count);
    }

    private function getWorkerMock(array $returns)
    {
        $worker = $this->getMock('Valera\\Worker\\WorkerInterface', array('run', 'setLogger'));
        $worker->expects($this->exactly(count($returns)))
            ->method('run')
            ->will(
                call_user_func_array(array($this, 'onConsecutiveCalls'), $returns)
            );

        return $worker;
    }
}
