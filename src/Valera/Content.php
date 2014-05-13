<?php

namespace Valera;

/**
 * Downloaded content
 */
final class Content implements Queueable
{
    /**
     * String content
     *
     * @var string
     */
    private $content;

    /**
     * MIME type of the content
     *
     * @var string
     */
    private $mimeType;

    /**
     * Content source
     *
     * @var Source
     */
    private $source;

    /**
     * Constructor
     *
     * @param string      $content
     * @param string|null $mimeType
     * @param Source      $source
     */
    public function __construct($content, $mimeType, Source $source)
    {
        if (!is_string($content)) {
            throw new \InvalidArgumentException(
                sprintf('Content must be a string, %s given', gettype($content))
            );
        }

        $this->content = $content;
        $this->mimeType = $mimeType;
        $this->source = $source;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getMimeType()
    {
        return $this->mimeType;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getType()
    {
        return $this->source->getType();
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
