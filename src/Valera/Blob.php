<?php

namespace Valera;

class Blob
{
    /**
     * @var \Valera\Resource
     */
    protected $resource;
    protected $path;

    public function __construct($resourceOrUrl, $path = null)
    {
        if (is_string($resourceOrUrl)) {
            $resourceOrUrl = new Resource($resourceOrUrl);
        }

        if (!$resourceOrUrl instanceof Resource) {
            throw new \InvalidArgumentException(
                '$resourceOrUrl must be instance of \Valera\Resource or URL'
            );
        }

        $this->resource = $resourceOrUrl;
        $this->setPath($path);
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function getHash()
    {
        return $this->resource->getHash();
    }

    public function setPath($path)
    {
        $this->path = $path;
    }
}
