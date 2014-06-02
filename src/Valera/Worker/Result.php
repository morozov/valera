<?php

namespace Valera\Worker;

class Result
{
    /**
     * Boolean value of the result, initially is undefined
     *
     * @var boolean|null
     */
    protected $status;

    /**
     * Failure reason
     *
     * @var string
     */
    protected $reason;

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

    /**
     * Marks item processing as failed
     *
     * @param string $reason Failure reason
     *
     * @return static
     */
    public function fail($reason)
    {
        if ($this->status === true) {
            throw new \LogicException(
                'Result is already marked as successful'
            );
        }

        $this->status = false;
        $this->reason = $reason;

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
     * Returns failure reason
     *
     * @return string
     */
    public function getReason()
    {
        $this->ensureFailure();

        return $this->reason;
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
