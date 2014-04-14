<?php

namespace Valera;

class Blob
{
    protected $resource;
    protected $path;

    public function __construct($resourceOrUrl, $path = null)
    {
        if (is_string($resourceOrUrl)) {
            $resourceOrUrl = new Resource($resourceOrUrl);
        }

        if (!$resourceOrUrl instanceof Resource) {
            throw new \InvalidArgumentException(
                '$resourceOrUrl must be instance of Resource or URL'
            );
        }

        $this->resource = $resourceOrUrl;
        $this->setPath($path);
    }

    public function setPath($path)
    {
        $this->path = $path;
    }
}
