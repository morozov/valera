<?php

namespace Valera\Value;

use Assert\Assertion;

/**
 */
abstract class ResourceData
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    protected $method;
    protected $headers;
    protected $payload;

    /**
     * @param string $method HTTP method to fetch resource
     * @param array $headers
     * @param array $payload
     *
     * @throws \Assert\AssertionFailedException
     */
    protected function __construct(
        $method = self::METHOD_GET,
        array $headers = array(),
        $payload = null
    ) {
        Assertion::string($method);
        $method = strtoupper($method);
        Assertion::inArray($method, array(self::METHOD_GET, self::METHOD_POST));

        Assertion::allString($headers);

        $this->method = $method;
        $this->headers = $headers;
        $this->payload = $payload;
    }
}
