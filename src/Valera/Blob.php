<?php

namespace Valera;

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
     * @param string           $path
     * @param \Valera\Resource $resource
     */
    public function __construct($path, Resource $resource = null)
    {
        $this->path = $path;
        $this->resource = $resource;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getResource()
    {
        return $this->resource;
    }
}
