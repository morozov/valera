<?php

namespace Valera;
use Valera\Resource;

class Content
{

    protected $content;
    protected $resource;

    public function __construct($content, Resource $resource)
    {
        if (!is_string($content)) {
            throw new \InvalidArgumentException(
                sprintf('Content must be a string, %s given', gettype($content))
            );
        }
        $this->content = $content;
        $this->resource = $resource;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getResource()
    {
        return $this->resource;
    }
}
