<?php

namespace Valera;

use Valera\Serialize\Serializer;

class Content implements Queueable
{
    protected $content;
    protected $source;

    public function __construct($content, Source $source)
    {
        if (!is_string($content)) {
            throw new \InvalidArgumentException(
                sprintf('Content must be a string, %s given', gettype($content))
            );
        }
        $this->content = $content;
        $this->source = $source;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function __toString()
    {
        return $this->getContent();
    }

    public function getType()
    {
        return $this->source->getType();
    }

    public function getSource()
    {
        return $this->source;
    }

    /**
     * Returns content hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->source->getHash();
    }

    public function accept(Serializer $serializer)
    {
        return $serializer->serializeContent($this);
    }
}
