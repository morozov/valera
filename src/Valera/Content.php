<?php

namespace Valera;

use Valera\Serialize\Serializer;

class Content implements Queueable
{
    protected $content;
    protected $type;
    protected $resource;

    public function __construct($content, $type, Resource $resource)
    {
        if (!is_string($content)) {
            throw new \InvalidArgumentException(
                sprintf('Content must be a string, %s given', gettype($content))
            );
        }
        $this->content = $content;
        $this->type = $type;
        $this->resource = $resource;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Returns content hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->resource->getHash();
    }

    public function accept(Serializer $serializer)
    {
        return $serializer->serializeContent($this);
    }
}
