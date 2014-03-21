<?php

namespace Valera\Parser\Result;

use LogicException;

class Proxy
{
    /**
     * Actual value of the result
     *
     * @var \Valera\Parser\Result\ResultInterface
     */
    protected $result;

    public function succeed($data)
    {
        $this->ensureUnresolved();
        $this->result = new Success($data);

        return $this->result;
    }

    public function fail($message)
    {
        $this->ensureUnresolved();
        $this->result = new Failure($message);
    }

    protected function ensureUnresolved()
    {
        if ($this->result) {
            throw new LogicException(
                'Result is already resolved'
            );
        }
    }
}
