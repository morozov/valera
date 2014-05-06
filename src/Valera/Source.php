<?php

namespace Valera;

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
     * @param string $type
     *
     * @param \Valera\Resource $resource
     * @throws \InvalidArgumentException
     */
    public function __construct($type, Resource $resource)
    {
        if (!is_string($type)) {
            throw new \InvalidArgumentException(
                sprintf('Type should be a string, %s given', gettype($type))
            );
        }

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
