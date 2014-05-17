<?php

namespace Valera;

use Assert\Assertion;

/**
 * Downloaded binary contents
 */
final class Blob
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var \Valera\Resource
     */
    private $resource;

    /**
     * Constructor
     *
     * @param string           $path     Path in blob storage
     * @param \Valera\Resource $resource Origin resource
     *
     * @throws \Assert\AssertionFailedException
     */
    public function __construct($path, Resource $resource = null)
    {
        Assertion::string($path);

        $this->path = $path;
        $this->resource = $resource;
    }

    /**
     * Returns path in blob storage
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns origin resource
     *
     * @return \Valera\Resource
     */
    public function getResource()
    {
        return $this->resource;
    }
}
