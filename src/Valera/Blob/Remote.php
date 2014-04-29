<?php

namespace Valera\Blob;

use Valera\Blob;
use Valera\Resource;

class Remote implements Blob
{
    /**
     * @var \Valera\Resource
     */
    protected $resource;

    public function __construct($resourceOrUrl)
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
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function getHash()
    {
        return $this->resource->getHash();
    }

    public function __toString()
    {
        return $this->resource->getUrl();
    }
}
