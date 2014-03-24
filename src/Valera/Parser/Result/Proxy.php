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

    /**
     * @return ResultInterface
     * @throws \LogicException
     */
    public function getResult()
    {
        if (!$this->result) {
            throw new LogicException(
                'Result is not yet resolved'
            );
        }

        return $this->result;
    }

    /**
     * @throws \LogicException
     */
    protected function ensureUnresolved()
    {
        if ($this->result) {
            throw new LogicException(
                'Result is already resolved'
            );
        }
    }
}
