<?php

namespace Valera;

final class Content implements Queueable
{
    private $content;
    private $source;

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

    public function getResource()
    {
        return $this->source->getResource();
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
}
