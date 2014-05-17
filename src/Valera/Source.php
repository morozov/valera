<?php

namespace Valera;

use Assert\Assertion;

/**
 */
final class Source implements Queueable
{
    /**
     * @var \Valera\Resource
     */
    private $resource;

    /**
     * @var string
     */
    private $type;

    /**
     * Constructor
     *
     * @param string $type               Source type, the name of the parser that
     *                                   should be applied to parse its contents
     * @param \Valera\Resource $resource The HTTP resource representing the source
     *
     * @throws \Assert\AssertionFailedException
     */
    public function __construct($type, Resource $resource)
    {
        Assertion::string($type);

        $this->resource = $resource;
        $this->type = $type;
    }

    /**
     * Returns the resource representing the source
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Returns the type of the source
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns source hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->resource->getHash();
    }
}
