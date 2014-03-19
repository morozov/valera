<?php

namespace Valera\Parser\Result;

use LogicException;
use Valera\Resource;

class Proxy
{
    const SUCCESS = 'success';
    const FAILURE = 'failure';

    /**
     * Actual value of the result
     *
     * @var \Valera\Parser\Result\ResultInterface
     */
    protected $result;

    /**
     * Result type
     *
     * @var string
     */
    protected $type;

    public function addResource(
        $type,
        $url,
        $method = Resource::METHOD_GET,
        array $headers = array(),
        array $data = array()
    ) {
        $this->checkFailure();

        if (!$this->result) {
            $this->result = new Success();
        }

        $this->result->addResource($type, $url, $method, $headers, $data);
    }

    public function fail($message)
    {
        $this->checkSuccess();
        $this->checkFailure();

        $this->type = self::FAILURE;
        $this->result = new Failure($message);
    }

    protected function checkSuccess()
    {
        if ($this->type === self::SUCCESS) {
            throw new LogicException(
                'Result is already resolved as success'
            );
        }
    }

    protected function checkFailure()
    {
        if ($this->type === self::FAILURE) {
            throw new LogicException(
                'Result is already resolved as failure'
            );
        }
    }
}
