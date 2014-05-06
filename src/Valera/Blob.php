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

    public function __construct($path, Resource $resource)
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
