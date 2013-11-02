<?php

namespace Valera;
use Valera\ContentInterface;
use Valera\ResourceInterface;

class Content implements ContentInterface
{

    protected $content;
    protected $resource;

    public function __construct($content, ResourceInterface $resource)
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
