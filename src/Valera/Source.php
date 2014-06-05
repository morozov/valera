<?php

namespace Valera;

/**
 * Base class for data sources. Contains origin resources. Derived classes may defined additional attributes
 * for data processing.
 */
abstract class Source implements Queueable
{
    /**
     * @var \Valera\Resource
     */
    private $resource;

    /**
     * Constructor
     *
     * @param \Valera\Resource $resource The HTTP resource representing the source
     *
     * @throws \Assert\AssertionFailedException
     */
    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Returns the resource representing the source
     */
    public function getResource()
    {
        return $this->resource;
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
