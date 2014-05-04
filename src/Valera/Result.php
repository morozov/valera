<?php

namespace Valera;

class Result
{
    /**
     * Boolean value of the result, initially is undefined
     *
     * @var boolean|null
     */
    protected $status;

    /**
     * Failure message|null
     *
     * @var string
     */
    protected $message;

    public function resolve()
    {
        if ($this->status === false) {
            throw new \LogicException(
                'Result is already marked as failed'
            );
        }

        $this->status = true;

        return $this;
    }

    public function fail($message = null)
    {
        if ($this->status === true) {
            throw new \LogicException(
                'Result is already marked as successful'
            );
        }

        $this->status = false;
        $this->message = $message;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getStatus()
    {
        if ($this->status === null) {
            $this->fail('The result was not explicitly resolved');
        }

        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getMessage()
    {
        $this->ensureFailure();

        return $this->message;
    }

    /**
     * @throws \LogicException
     */
    protected function ensureSuccess()
    {
        if (!$this->getStatus()) {
            throw new \LogicException(
                'Result is marked as failed'
            );
        }
    }

    /**
     * @throws \LogicException
     */
    protected function ensureFailure()
    {
        if ($this->getStatus()) {
            throw new \LogicException(
                'Result is marked as success'
            );
        }
    }
}
