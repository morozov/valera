<?php

namespace Valera\Value;

use Valera\Resource;

final class Reference extends ResourceData
{
    private $reference;

    /**
     * @param string $reference Reference to another resource
     * @param string $method HTTP method to fetch resource
     * @param array $headers
     * @param array $payload
     *
     * @throws \Assert\AssertionFailedException
     */
    public function __construct(
        $reference,
        $method = self::METHOD_GET,
        array $headers = array(),
        $payload = null
    ) {
        $this->reference = $reference;

        parent::__construct($method, $headers, $payload);
    }

    /**
     * Returns resource reference
     *
     * @param string $referrer
     *
     * @return \Valera\Resource
     * @throws \Assert\AssertionFailedException
     */
    public function getResource($referrer)
    {
        return new Resource(
            $this->reference,
            $referrer,
            $this->method,
            $this->headers,
            $this->payload
        );
    }
}
