<?php

namespace Valera\Result;

use LogicException;

class Proxy
{
    /**
     * Actual value of the result
     *
     * @var \Valera\Result\ResultInterface
     */
    protected $result;

    public function resolve()
    {
        $this->ensureUnresolved();
        $this->result = $this->getSuccess();

        return $this->result;
    }

    public function fail($message)
    {
        $this->ensureUnresolved();
        $this->result = $this->getFailure($message);

        return $this->result;
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
     * @return Success
     */
    protected function getSuccess()
    {
        return new Success();
    }

    /**
     * @param mixed $message
     *
     * @return Failure
     */
    protected function getFailure($message)
    {
        return new Failure($message);
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
