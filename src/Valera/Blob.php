<?php

namespace Valera;

final class Blob
{
    /**
     * @var \Valera\Resource
     */
    private $resource;

    /**
     * @var string
     */
    private $path;

    public function __construct(Resource $resource, $path)
    {
        $this->resource = $resource;
        $this->path = $path;
    }

    public function getResource()
    {
        return $this->path;
    }

    public function getPath()
    {
        return $this->path;
    }
}
